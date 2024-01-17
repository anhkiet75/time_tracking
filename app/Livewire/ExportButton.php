<?php

namespace App\Livewire;

use App\Filament\Helper\CustomExport;
use Faker\Provider\ar_EG\Text;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Maatwebsite\Excel\Excel;

class ExportButton extends Component implements HasForms
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
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.export-button');
    }
}
