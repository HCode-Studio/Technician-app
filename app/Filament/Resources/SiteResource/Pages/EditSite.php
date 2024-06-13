<?php

namespace App\Filament\Resources\SiteResource\Pages;

use App\Filament\Resources\SiteResource;
use App\Models\User;
use COM;
use Filament\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Re assigner le possesseur')->color('secondary')
                ->form([
                    Fieldset::make('Re assigner le possesseur')
                        ->schema([
                            Select::make('user_id')
                                ->label('Possesseur')
                                ->required()
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable()
                        ])
                ])
                ->before(function (Model $record, array $data) {
                    $record->user_id = $data['user_id'];
                    $record->save();
                })
                ,
        ];
    }

}
