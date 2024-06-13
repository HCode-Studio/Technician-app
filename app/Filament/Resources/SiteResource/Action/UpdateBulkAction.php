<?php

namespace App\Filament\Resources\SiteResource\Action;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UpdateBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'all-completed';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Marquer comme terminé');

        $this->modalHeading(fn (): string => 'Marquer les todos en terminé');

        $this->modalSubmitActionLabel('Marquer comme terminé');

        $this->successNotificationTitle('Terminé');

        $this->color('success');

        $this->icon(FilamentIcon::resolve('actions::detach-action') ?? 'heroicon-m-x-mark');

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve('actions::detach-action.modal') ?? 'heroicon-o-x-mark');

        $this->action(function (): void {
            $this->process(function (Collection $records, Table $table): void {
                $records->each(
                    function (Model $record) {
                         $record->completed = 'done';
                         $record->save();
                    }
                );

            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
