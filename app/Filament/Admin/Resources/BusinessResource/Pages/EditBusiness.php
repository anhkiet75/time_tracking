<?php

namespace App\Filament\Admin\Resources\BusinessResource\Pages;

use App\Filament\Admin\Resources\BusinessResource;
use App\Filament\Helper\BusinessHelper;
use App\Models\Business;
use App\Models\BusinessQRCodeRange;
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

    protected function handleRecordUpdate(Model $model, array $data): Model
    {
        $qr_code_ranges = BusinessHelper::convertInputToRangesArray($data['business_range']);
        BusinessQRCodeRange::where('business_id', $model->id)->delete();

        BusinessHelper::createQRCodeRanges($model->id, $qr_code_ranges);
        $model->update($data);
        Notification::make()
            ->title('Updated.')
            ->info()
            ->send();
        return $model;
    }
}
