<?php

namespace App\Filament\Resources\AttachmentResource\Pages;

use App\Filament\Resources\AttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateAttachment extends CreateRecord
{
    protected static string $resource = AttachmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['name'])) {
            $data['path'] = $data['name'];
            $data['size'] = Storage::disk('public')->size($data['name']);
            $data['type'] = Storage::mimeType($data['path']);
            $data['url'] = config("APP_URL") . Storage::url($data['path']);
        }
        return $data;
    }

}
