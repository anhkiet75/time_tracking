<?php

namespace App\Filament\Helper;

use App\Models\Checkin;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Maatwebsite\Excel\Exceptions\NoFilenameGivenException;
use Maatwebsite\Excel\Exporter;
use pxlrbt\FilamentExcel\Exports\Concerns\CanQueue;

class ExportFromView implements FromView, WithColumnWidths
{
    public $data;
    function __construct($data)
    {
        $this->data = $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function view(): View
    {
        return view('exports.timesheets', [
            'user' => auth()->user(),
            'timesheets' => Checkin::all(),
            'data' => $this->data
        ]);
    }
}

class CustomExport extends ExcelExport 
{
    private function getExporter(): Exporter
    {
        return app(Exporter::class);
    }

    public function downloadExport(string $fileName = null, string $writerType = null, array $headers = null)
    {
        $headers    = $headers ?? [];
        $fileName   = $fileName ?? 'fileName' ?? null;
        $writerType = $writerType ?? $this->writerType ?? null;
        if (null === $fileName) {
            throw new NoFilenameGivenException();
        }
        return $this->getExporter()->download(new ExportFromView($this->query()->get()), $fileName, $writerType, $headers);
    }
}
