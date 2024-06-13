<?php

namespace App\Filament\Resources\AttachmentResource\Pages;

use App\Filament\Resources\AttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditAttachment extends EditRecord
{
    protected static string $resource = AttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        dd($data, 'EditAttachment');
        if (isset($data['attachment'])) {
            $data['path'] = $data['attachment'];
            $data['name'] = pathinfo($data['path'], PATHINFO_BASENAME);
            $data['size'] = Storage::size($data['path']);
            $data['type'] = Storage::mimeType($data['path']);
            $data['url'] = Storage::url($data['path']);
            $data['notes'] = json_encode(['Uploaded by ' . auth()->user()->name]);
            $data['attachment'] = null;
            
        }
        return parent::handleRecordUpdate($record, $data);
    }
}
