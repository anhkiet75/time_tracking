<?php

namespace App\Livewire\Location;

use App\Models\Checkin;
use App\Models\Location;
use App\Models\User;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Closure;
use DateTime;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CheckinLocation extends Component implements HasForms
{
    use InteractsWithForms;

    public $data = null;
    public $location = null;
    public $user = null;

    public function mount($qr_code): void
    {
        $this->location = Location::where('qr_code', $qr_code)->firstOrFail();
        $this->user = auth()->user();
        if ($this->user->is_checkin) {
            $this->data = Checkin::where('user_id', $this->user->id)->latest()->first()->toArray();
        }
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Allow GPS')
                        ->schema([
                            Map::make('location')
                                ->autocomplete(
                                    fieldName: 'current_location',
                                )
                                ->geolocate()
                                ->draggable(false)
                                ->geolocateLabel('Get Location') // overrides the default label for geolocate button
                                ->geolocateOnLoad(true, false)
                                ->height(fn () => '100px')
                                ->autocompleteReverse(true),
                            TextInput::make('current_location')->required()->readOnly()
                        ]),
                    Wizard\Step::make('Authentication')
                        ->schema([
                            TextInput::make('pin_code')
                                ->required()
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, Closure $fail) {
                                            if (empty($value) ||  $value !==  auth()->user()->pin_code) {
                                                $fail('The PIN CODE is invalid.');
                                            }
                                        };
                                    },
                                ])
                        ])
                        ->hidden(fn () => auth()->user()->is_check_pin_code),
                    Wizard\Step::make('Check in/Check out')
                        ->schema([
                            Grid::make()
                                ->columns(1)
                                ->schema([
                                    TextInput::make('checkin_time')
                                        ->readOnly()
                                        ->suffixAction(
                                            Action::make('setCheckin')
                                                ->icon('heroicon-o-check-badge')
                                                ->requiresConfirmation()
                                                ->action(function (Set $set, $state) {
                                                    $set('checkin_time', date('Y-m-d H:i:s'));
                                                })
                                        )
                                        ->hidden(fn () => $this->user->is_checkin == true),
                                    TextInput::make('checkout_time')
                                        ->readOnly()
                                        ->suffixAction(
                                            Action::make('setCheckout')
                                                ->icon('heroicon-o-check-badge')
                                                ->requiresConfirmation()
                                                ->action(function (Set $set, $state) {
                                                    $set('checkout_time', date('Y-m-d H:i:s'));
                                                })
                                        )
                                        ->hidden(fn () => $this->user->is_checkin == false),
                                ])->hidden(fn () => !$this->location->can_logtime),
                            TextInput::make('checkpoint_time')
                                ->readOnly()
                                ->suffixAction(
                                    Action::make('setCheck')
                                        ->icon('heroicon-o-check-badge')
                                        ->requiresConfirmation()
                                        ->action(function (Set $set, $state) {
                                            $set('checkpoint_time', date('Y-m-d H:i:s'));
                                        })
                                )
                                ->hidden(fn () => $this->location->can_check == false)
                        ])->hidden(fn () => !$this->user->allow_qr_code_entry)
                ])
                    ->persistStepInQueryString()
                    ->submitAction(new HtmlString('<button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);" type="submit" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50 fi-ac-btn-action">Submit</button>'))
            ])
            ->statePath('data')
            ->model(Checkin::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $data['location_id'] = $this->location->id;
        $data['user_id'] = $this->user->id;
        $data['lat'] = $data['location']['lat'];
        $data['lng'] = $data['location']['lng'];

        if ($this->user->is_checkin) {
            $checkin = Checkin::where('user_id', $this->user->id)->latest()->first();
            $checkin->checkout_time = $this->data['checkout_time'];
            $checkin->lat = $data['lat'];
            $checkin->lng = $data['lng'];
            $checkin_time = new DateTime($checkin->checkin_time);
            $checkout_time = new DateTime($checkin->checkout_time);
            $interval =  $checkin_time->diff($checkout_time);
            $checkin->log_time = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            $checkin->save();
        } else {
            $record = Checkin::create($data);
            $this->form->model($record)->saveRelationships();
        }

        $this->user->is_checkin = !$this->user->is_checkin;
        $this->user->is_check_pin_code = true;
        $this->user->save();

        Notification::make()
            ->title('Saved successfully')
            ->info()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.location.checkin-location');
    }
}
