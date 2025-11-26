<?php

namespace BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs;

use BackedEnum;
use BinaryBuilds\FilamentFailedJobs\FilamentFailedJobsPlugin;
use BinaryBuilds\FilamentFailedJobs\Models\FailedJob;
use BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs\Pages\ListFailedJobs;
use BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs\Pages\ViewFailedJob;
use BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs\Schemas\FailedJobInfolist;
use BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs\Tables\FailedJobsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string | BackedEnum | null $navigationIcon = null;

    public static function infolist(Schema $schema): Schema
    {
        return FailedJobInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FailedJobsTable::configure($table)
            ->defaultSort('id', 'desc');
    }

    private static function getPlugin(): FilamentFailedJobsPlugin
    {
        /** @var FilamentFailedJobsPlugin */
        return filament('failed-jobs');
    }

    public static function canAccess(): bool
    {
        return static::getPlugin()->isAuthorized();
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return static::getPlugin()->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return static::getPlugin()->getNavigationLabel() ?: 'Failed Jobs';
    }

    public static function getNavigationSort(): ?int
    {
        return static::getPlugin()->getNavigationSort();
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return static::getPlugin()->getNavigationIcon();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFailedJobs::route('/'),
            'view' => ViewFailedJob::route('/{record}'),
        ];
    }
}
