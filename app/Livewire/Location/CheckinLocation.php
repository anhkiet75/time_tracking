<?php

namespace App\Livewire\Location;

use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class CheckinLocation extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Wizard::make([
                Wizard\Step::make('Authentication')
                ->schema([
                    TextInput::make('qr_code')
                ]),
                Wizard\Step::make('Check in/Checkout')
                ->schema([
                    TextInput::make('address')
                ])
            ])
            ->startOnStep(2)
            ])
            ->statePath('data')
            ->model(Location::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Location::create($data);

        $this->form->model($record)->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.location.checkin-location');
    }
}