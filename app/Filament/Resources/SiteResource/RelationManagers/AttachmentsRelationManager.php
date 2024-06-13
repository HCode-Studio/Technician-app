<?php

namespace App\Filament\Resources\SiteResource\RelationManagers;

use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Ajouter un Fichier ou une image')
                ->schema([
                    FileUpload::make('name')
                    ->disk('public')
                    ->required()
                    ->directory('attachments'),
                ])->columns(1),
            Fieldset::make('Notes')
                ->schema([
                    KeyValue::make('notes'),
                ])->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('name'),
                Tables\Columns\TextColumn::make('notes'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    if (isset($data['name'])) {
                        $data['path'] = $data['name'];
                        $data['size'] = Storage::disk('public')->size($data['name']);
                        $data['type'] = Storage::mimeType($data['path']);
                        $data['url'] = config("APP_URL") . Storage::url($data['path']);
                    }
                    return $data;
                }),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('secondary'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
