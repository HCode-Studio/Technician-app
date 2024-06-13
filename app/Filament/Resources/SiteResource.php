<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Filament\Resources\SiteResource\RelationManagers;
use App\Models\Site;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static array $adress = [];
  

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->placeholder('Enter the name of the site'),

                Fieldset::make('Localisation')
                    ->schema([
                        Select::make('address')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                $response = Http::get("https://api-adresse.data.gouv.fr/search/?q=$search")->json();
                                return collect($response['features'])->pluck('properties.label', 'properties.label')->toArray();
                            })
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Fetch location details for the selected address
                                $response = Http::get("https://api-adresse.data.gouv.fr/search/?q=$state")->json();
                                if (isset($response['features'][0])) {
                                    $locationDetails = $response['features'][0]['properties'];
                                    // Set the values of city, state, and zip dynamically
                                    $set('city', $locationDetails['city'] ?? '');
                                    $set('state', $locationDetails['context'] ?? '');
                                    $set('zip', $locationDetails['postcode'] ?? '');
                                }
                            }),
                        Forms\Components\TextInput::make('state')
                            ->required(),
                        Forms\Components\TextInput::make('city')
                            ->required(),
                        Forms\Components\TextInput::make('zip')
                            ->required(),
                    ])
                    ->columns(4),
                Fieldset::make("Notes")
                    ->schema([
                        TagsInput::make('badges')
                            ->reactive()
                            ->label('Badges'),
                        KeyValue::make('notes')
                            ->keyLabel('Note')
                            ->valueLabel('Value')
                    ])
                    ->columns(1),
                
                Fieldset::make('Technicien')
                    ->schema([
                        Select::make('user_id')
                            ->label('Technicien')
                            ->options(User::all()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('zip')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                    
            ])
            // list only the sites of the current user
            ->filters([

                SelectFilter::make('user_id')
                    ->options(User::all()->pluck('name', 'id'))
                    ->label('Possesseur')
                    ->default(auth()->user()->id)
                    ->query(function (Builder $query) {
                        return $query->where('user_id', auth()->user()->id);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
              
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttachmentsRelationManager::class,
            RelationManagers\TodosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
        ];
    }
}
