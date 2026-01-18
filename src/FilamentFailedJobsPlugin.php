<?php

namespace BinaryBuilds\FilamentFailedJobs;

use BackedEnum;
use BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs\FailedJobResource;
use BinaryBuilds\FilamentFailedJobs\Traits\PluginVariables;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Tables\Enums\FiltersLayout;
use UnitEnum;

class FilamentFailedJobsPlugin implements Plugin
{
    use PluginVariables;

    public bool $horizon = false;

    public bool $hideConnectionOnIndex = false;

    public bool $hideQueueOnIndex = false;

    public bool $poll = true;

    public string $pollingInterval = '60s';

    public FiltersLayout $filtersLayout = FiltersLayout::Dropdown;

    public function getId(): string
    {
        return 'failed-jobs';
    }

    public function usingHorizon(bool $horizon = true): FilamentFailedJobsPlugin
    {
        $this->horizon = $horizon;

        return $this;
    }

    public function isUsingHorizon(): bool
    {
        return $this->horizon;
    }

    public function hideConnectionOnIndex(bool $hideConnectionOnIndex = true): FilamentFailedJobsPlugin
    {
        $this->hideConnectionOnIndex = $hideConnectionOnIndex;

        return $this;
    }

    public function hideQueueOnIndex(bool $hideQueueOnIndex = true): FilamentFailedJobsPlugin
    {
        $this->hideQueueOnIndex = $hideQueueOnIndex;

        return $this;
    }

    public function filtersLayout(FiltersLayout $layout): FilamentFailedJobsPlugin
    {
        $this->filtersLayout = $layout;

        return $this;
    }

    public function getFiltersLayout(): FiltersLayout
    {
        return $this->filtersLayout;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            FailedJobResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function authorize(bool | Closure $callback): static
    {
        $this->authorized = $callback;

        return $this;
    }

    public function navigationGroup(string | UnitEnum | Closure $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(string | BackedEnum | Closure $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationLabel(string | Closure $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function navigationSort(int | Closure $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function withPolling(bool $poll = true): static
    {
        $this->poll = $poll;

        return $this;
    }

    public function pollingInterval(string $interval): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function shouldPoll(): bool
    {
        return $this->poll;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
