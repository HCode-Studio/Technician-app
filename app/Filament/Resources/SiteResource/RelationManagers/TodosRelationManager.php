<?php

namespace App\Filament\Resources\SiteResource\RelationManagers;

use App\Filament\Resources\SiteResource\Action\UpdateBulkAction;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TodosRelationManager extends RelationManager
{
    protected static string $relationship = 'todos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Status')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Select::make('completed')
                            ->label('Status')
                            ->options([
                                'todo' => 'To do',
                                'doing' => 'Doing',
                                'done' => 'Done',
                            ])
                            ->default('todo')
                            ->required()
                    ]),
                Section::make('Tache')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->nullable(),
                    ]),
                Section::make('Badge et Notes')
                ->columns(1)
                ->schema([
                    Forms\Components\TagsInput::make('tags')
                    ->label('Tags')
                    ->placeholder('Ajouter des tags')
                    ->separator(',') 
                    ->suggestions([
                        'urgent',
                        'important',
                        'optional',
                    ]),
                    KeyValue::make('badges')
                    ->label('Badges'),
                ]),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\IconColumn::make('completed')
                    ->label('Status') 
                    ->icon(fn (string $state): string => match ($state) {
                        'todo' => 'heroicon-o-pencil',
                        'doing' => 'heroicon-o-clock',
                        'done' => 'heroicon-o-check-circle',
                    })->color(fn (string $state): string => match ($state) {
                        'todo' => 'warning',
                        'doing' => 'info',
                        'done' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at'),


            ])
            ->filters([
                SelectFilter::make('completed')
                    ->options([
                        'todo' => 'To do',
                        'doing' => 'Doing',
                        'done' => 'Done',
                    ])
                    ->label('Status'),

                SelectFilter::make('created_at')
                    ->baseQuery(fn (Builder $query) => $query->orderBy('created_at', 'desc'))
                
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('secondary'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    UpdateBulkAction::make()

                ]),
            ]);
    }
}
