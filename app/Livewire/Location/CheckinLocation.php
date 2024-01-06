<?php

namespace App\Livewire\Location;

use App\Models\Checkin;
use App\Models\Location;
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
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;

class CheckinLocation extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $location = null;

    public function mount($qr_code): void
    {
        $this->form->fill();
        $this->location = Location::where('qr_code', $qr_code)->firstOrFail();
        // $this->data = Checkin::firstOrCreate([
        //     'location_id' => $location->id,
        //     'user_id' => auth()->user()->id
        // ])->toArray();
        // dd($this->data);
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
                                ->geolocateOnLoad(true, true)
                                ->height(fn () => '100px')
                                ->autocompleteReverse(true),
                            TextInput::make('current_location')->readOnly()
                        ]),
                    Wizard\Step::make('Authentication')
                        ->schema([
                            TextInput::make('pin_code')
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, Closure $fail) {
                                            if ($value !=  auth()->user()->pin_code) {
                                                $fail('The PIN CODE is invalid.');
                                            }
                                        };
                                    },
                                ])
                        ]),
                    Wizard\Step::make('Check in/Checkout')
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
                                ->hidden(fn () => $this->location->can_logtime == false),

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
                                ->hidden(fn () => $this->location->can_logtime == false),

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
                        ])
                ])
            ])
            ->statePath('data')
            ->model(Checkin::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Checkin::create($data);

        $this->form->model($record)->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.location.checkin-location');
    }
}
