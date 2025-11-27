<?php

namespace BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FailedJobInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Exception')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->collapsible()
                                    ->schema([
                                        TextEntry::make('exception')
                                            ->hiddenLabel()
                                            ->formatStateUsing(function ($state) {
                                                return '<pre class="whitespace-pre-wrap break-words text-sm font-mono bg-gray-50 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto">' . htmlspecialchars($state) . '</pre>';
                                            })
                                            ->html(),
                                    ]),

                                Section::make('Payload')
                                    ->icon('heroicon-o-code-bracket')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        TextEntry::make('payload')
                                            ->hiddenLabel()
                                            ->formatStateUsing(function ($state) {
                                                return '<pre class="whitespace-pre-wrap break-words text-sm font-mono bg-gray-50 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto">' . htmlspecialchars(json_encode(json_decode($state, true), JSON_PRETTY_PRINT)) . '</pre>';
                                            })
                                            ->html(),
                                    ]),
                            ]),

                        Section::make('Job Details')
                            ->icon('heroicon-o-briefcase')
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('payload')
                                    ->label('Job Class')
                                    ->formatStateUsing(function ($state) {
                                        return json_decode($state, true)['displayName'];
                                    })
                                    ->weight('bold'),

                                TextEntry::make('uuid')
                                    ->label('UUID')
                                    ->copyable(),

                                TextEntry::make('id')
                                    ->label('ID'),

                                TextEntry::make('connection'),

                                TextEntry::make('queue'),

                                TextEntry::make('failed_at')
                                    ->label('Failed At')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),
            ]);
    }
}
