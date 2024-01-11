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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
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
        if (isset(auth()->user()->is_checkin) && auth()->user()->is_checkin) {
            $this->data = Checkin::where('user_id', auth()->user()->id)->latest()->first()->toArray();
        }
        $this->form->fill();
    }

    public static function circle_distance($lat1, $lon1, $lat2, $lon2)
    {
        $rad = M_PI / 180;
        return acos(sin($lat2 * $rad) * sin($lat1 * $rad) + cos($lat2 * $rad) * cos($lat1 * $rad) * cos($lon2 * $rad - $lon1 * $rad)) * 6371; // Kilometers
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Authentication')
                        ->schema([
                            TextInput::make('pin_code')
                                ->length(6)
                                ->required()
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, Closure $fail) {
                                            $user = User::where('pin_code', $value)
                                                ->where('business_id', $this->location->business_id)
                                                ->first();
                                            if (empty($user)) {
                                                $fail('The PIN CODE is invalid.');
                                            } else {
                                                Auth::login($user);
                                                $user->use_pin_code = true;
                                                $user->save();
                                            }
                                        };
                                    },
                                ])
                        ])
                        ->hidden(fn () => auth()->check()),
                    Wizard\Step::make('Allow GPS')
                        ->schema([
                            Map::make('location')
                                ->autocomplete(
                                    fieldName: 'current_location',
                                )
                                ->geolocate()
                                ->draggable(false)
                                ->geolocateLabel('Get Location')
                                ->geolocateOnLoad(true, false)
                                ->height(fn () => '100px')
                                ->autocompleteReverse(true),
                            TextInput::make('current_location')
                                ->readOnly()
                                ->required()
                                ->rules([
                                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                        if ($this->location->enable_gps) {
                                            $lat = $this->location->lat;
                                            $lng = $this->location->lng;
                                            $radius = $this->location->radius;
                                            $current_lat = $get('location')['lat'];
                                            $current_lng = $get('location')['lng'];
                                            $distance = round(self::circle_distance($lat, $lng, $current_lat, $current_lng), 3) * 1000; //meters
                                            if ($distance > $radius) {
                                                $fail("You are not near the checkpoint ( {$distance}  meters far away)");
                                            }
                                        }
                                    },
                                ])
                        ]),
                    Wizard\Step::make('Check')
                        ->label(auth()->check() ? (auth()->user()->is_checkin ? 'Check out' : 'Check in') : 'Check')
                        ->schema([
                            Grid::make()
                                ->columns(1)
                                ->schema([
                                    TextInput::make('checkin_time')
                                        ->label('Check in at location')
                                        ->readOnly()
                                        ->suffixAction(
                                            Action::make('setCheckin')
                                                ->icon('heroicon-o-check-badge')
                                                ->action(function (Set $set, $state) {
                                                    $set('checkin_time', date('Y-m-d H:i:s'));
                                                    $this->setCheckin();
                                                })
                                        )
                                        ->hidden(fn () => auth()->check() && auth()->user()->is_checkin == true),
                                    TextInput::make('checkout_time')
                                        ->label('Check out')
                                        ->readOnly()
                                        ->suffixAction(
                                            Action::make('setCheckout')
                                                ->icon('heroicon-o-check-badge')
                                                ->action(function (Set $set, $state) {
                                                    $set('checkout_time', date('Y-m-d H:i:s'));
                                                    $this->setCheckout();
                                                })
                                        )
                                        ->hidden(fn () => auth()->check() && auth()->user()->is_checkin ==  false),
                                    TextInput::make('break_time')
                                        ->numeric()
                                        ->label('Break time')
                                        ->suffix('minutes')
                                        ->suffixAction(
                                            Action::make('setBreaktime')
                                                ->icon('heroicon-o-check-badge')
                                                ->action(function () {
                                                    $this->setBreakTime();
                                                })
                                        )
                                        ->hidden(fn () => !$this->location->can_break),
                                ])->hidden(fn () => !$this->location->can_logtime),
                            TextInput::make('checkpoint_time')
                                ->label('Checkpoint checked')
                                ->readOnly()
                                ->suffixAction(
                                    Action::make('setCheck')
                                        ->icon('heroicon-o-check-badge')
                                        ->action(function (Set $set, $state) {
                                            $set('checkpoint_time', date('Y-m-d H:i:s'));
                                            $this->setCheckpoint();
                                        })
                                )
                                ->hidden(fn () => $this->location->can_logtime)
                        ])
                        ->hidden(fn () => auth()->check() && !auth()->user()->allow_qr_code_entry)
                ])
                // ->submitAction(new HtmlString('<button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);" type="submit" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50 fi-ac-btn-action">Submit</button>'))
            ])
            ->statePath('data')
            ->model(Checkin::class);
    }

    public function toggleIsCheckin()
    {
        $user = User::find(auth()->user()->id);
        $user->is_checkin = !$user->is_checkin;
        $user->save();
    }

    public function calculateLogTime($checkin_time, $checkout_time)
    {
        $checkin_time = new DateTime($checkin_time);
        $checkout_time = new DateTime($checkout_time);
        $interval =  $checkin_time->diff($checkout_time);
        return ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
    }

    public function setBreakTime()
    {
        $checkin = Checkin::where('user_id', auth()->user()->id)->latest()->first();
        $checkin->break_time = $this->data['break_time'];
        $checkin->save();
        Notification::make()
            ->title('Add break time successfully')
            ->info()
            ->send();
        return redirect(request()->header('Referer'));
    }

    public function setCheckin()
    {
        $data = $this->form->getState();
        $data['location_id'] = $this->location->id;
        $data['user_id'] = auth()->user()->id;
        $data['lat'] = $data['location']['lat'];
        $data['lng'] = $data['location']['lng'];
        $record = Checkin::create($data);
        $this->form->model($record)->saveRelationships();
        $this->toggleIsCheckin();
        Notification::make()
            ->title('Check in successfully')
            ->info()
            ->send();
        return redirect(request()->header('Referer'));
    }

    public function setCheckout()
    {
        $data = $this->form->getState();
        $data['location_id'] = $this->location->id;
        $data['user_id'] = auth()->user()->id;
        $data['lat'] = $data['location']['lat'];
        $data['lng'] = $data['location']['lng'];
        $checkin = Checkin::where('user_id', auth()->user()->id)->latest()->first();
        $checkin->checkout_time = $this->data['checkout_time'];
        $checkin->lat = $data['lat'];
        $checkin->lng = $data['lng'];
        $checkin->log_time = $this->calculateLogTime($checkin->checkin_time, $checkin->checkout_time);
        $checkin->save();
        $this->toggleIsCheckin();
        Notification::make()
            ->title('Check out successfully')
            ->info()
            ->send();
        return redirect(request()->header('Referer'));
    }

    public function setCheckpoint()
    {
        $checkin = Checkin::where('user_id', auth()->user()->id)
            ->where('location_id', $this->location->id)
            ->latest()->first();
        if (is_null($checkin)) {
            $checkin = new Checkin;
        }
        $checkin->checkpoint_time = $this->data['checkpoint_time'];
        $checkin->save();
        Notification::make()
            ->title('Checkpoint checked successfully')
            ->info()
            ->send();
        return redirect(request()->header('Referer'));
    }

    public function render(): View
    {
        return view('livewire.location.checkin-location');
    }
}
