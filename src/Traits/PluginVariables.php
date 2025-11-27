<?php

declare(strict_types=1);

namespace BinaryBuilds\FilamentFailedJobs\Traits;

use BackedEnum;
use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

trait PluginVariables
{
    use EvaluatesClosures;

    public bool | Closure $authorized = true;

    public string | UnitEnum | Closure | null $navigationGroup = null;

    public string | BackedEnum | Closure $navigationIcon = Heroicon::QueueList;

    public string | Closure | null $navigationLabel = null;

    public int | Closure $navigationSort = 9999;

    public function isAuthorized(): bool
    {
        return (bool) $this->evaluate($this->authorized);
    }

    public function getNavigationGroup(): string | UnitEnum | null
    {
        return $this->evaluate($this->navigationGroup);
    }

    public function getNavigationIcon(): string | BackedEnum
    {
        /** @var string|BackedEnum */
        return $this->evaluate($this->navigationIcon);
    }

    public function getNavigationLabel(): string
    {
        /** @var string */
        return $this->evaluate($this->navigationLabel) ?? '';
    }

    public function getNavigationSort(): int
    {
        /** @var int */
        return $this->evaluate($this->navigationSort);
    }
}
