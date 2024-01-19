<?php

namespace App\Filament\Admin\Resources\BusinessResource\Pages;

use App\Filament\Admin\Resources\BusinessResource;
use App\Filament\Helper\BusinessHelper;
use App\Models\Business;
use App\Models\BusinessQRCodeRange;
use App\Models\Location;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBusiness extends EditRecord
{
    protected static string $resource = BusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $ranges = BusinessQRCodeRange::where('business_id', $data['id'])->orderBy('start_range')->get();
        $data['business_range'] = BusinessHelper::convertRangesToString($ranges);
        return $data;
    }

    protected function handleRecordUpdate(Model $model, array $data): Model
    {
        $qr_code_ranges = BusinessHelper::convertStringToRangesArray($data['business_range']);
        $merged_ranges = BusinessHelper::mergeRanges($qr_code_ranges);
        BusinessQRCodeRange::where('business_id', $model->id)->delete();
        BusinessHelper::createQRCodeRanges($model->id, $merged_ranges);
        $model->update($data);
        return $model;
    }

    protected function afterSave(): void
    {
        $this->js('window.location.reload()');
    }

}
