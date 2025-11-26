<?php

declare(strict_types=1);

use BinaryBuilds\FilamentFailedJobs\FilamentFailedJobsPlugin;
use Filament\Tables\Enums\FiltersLayout;

beforeEach(function () {
    $this->plugin = FilamentFailedJobsPlugin::make();
});

describe('horizon configuration', function () {
    it('defaults to not using Horizon', function () {
        expect($this->plugin->isUsingHorizon())->toBeFalse();
    });

    it('can enable Horizon mode', function () {
        $this->plugin->usingHorizon(true);

        expect($this->plugin->isUsingHorizon())->toBeTrue();
    });

    it('can disable Horizon mode', function () {
        $this->plugin->usingHorizon(true);
        $this->plugin->usingHorizon(false);

        expect($this->plugin->isUsingHorizon())->toBeFalse();
    });

    it('returns self for method chaining', function () {
        $result = $this->plugin->usingHorizon(true);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });

    it('enables Horizon by default when called without argument', function () {
        $this->plugin->usingHorizon();

        expect($this->plugin->isUsingHorizon())->toBeTrue();
    });
});

describe('hideConnectionOnIndex configuration', function () {
    it('defaults to showing connection column', function () {
        expect($this->plugin->hideConnectionOnIndex)->toBeFalse();
    });

    it('can hide connection column', function () {
        $this->plugin->hideConnectionOnIndex(true);

        expect($this->plugin->hideConnectionOnIndex)->toBeTrue();
    });

    it('can show connection column after hiding', function () {
        $this->plugin->hideConnectionOnIndex(true);
        $this->plugin->hideConnectionOnIndex(false);

        expect($this->plugin->hideConnectionOnIndex)->toBeFalse();
    });

    it('returns self for method chaining', function () {
        $result = $this->plugin->hideConnectionOnIndex(true);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });

    it('hides by default when called without argument', function () {
        $this->plugin->hideConnectionOnIndex();

        expect($this->plugin->hideConnectionOnIndex)->toBeTrue();
    });
});

describe('hideQueueOnIndex configuration', function () {
    it('defaults to showing queue column', function () {
        expect($this->plugin->hideQueueOnIndex)->toBeFalse();
    });

    it('can hide queue column', function () {
        $this->plugin->hideQueueOnIndex(true);

        expect($this->plugin->hideQueueOnIndex)->toBeTrue();
    });

    it('can show queue column after hiding', function () {
        $this->plugin->hideQueueOnIndex(true);
        $this->plugin->hideQueueOnIndex(false);

        expect($this->plugin->hideQueueOnIndex)->toBeFalse();
    });

    it('returns self for method chaining', function () {
        $result = $this->plugin->hideQueueOnIndex(true);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });

    it('hides by default when called without argument', function () {
        $this->plugin->hideQueueOnIndex();

        expect($this->plugin->hideQueueOnIndex)->toBeTrue();
    });
});

describe('filtersLayout configuration', function () {
    it('defaults to Dropdown layout', function () {
        expect($this->plugin->getFiltersLayout())->toBe(FiltersLayout::Dropdown);
    });

    it('can set AboveContent layout', function () {
        $this->plugin->filtersLayout(FiltersLayout::AboveContent);

        expect($this->plugin->getFiltersLayout())->toBe(FiltersLayout::AboveContent);
    });

    it('can set AboveContentCollapsible layout', function () {
        $this->plugin->filtersLayout(FiltersLayout::AboveContentCollapsible);

        expect($this->plugin->getFiltersLayout())->toBe(FiltersLayout::AboveContentCollapsible);
    });

    it('can set BelowContent layout', function () {
        $this->plugin->filtersLayout(FiltersLayout::BelowContent);

        expect($this->plugin->getFiltersLayout())->toBe(FiltersLayout::BelowContent);
    });

    it('returns self for method chaining', function () {
        $result = $this->plugin->filtersLayout(FiltersLayout::AboveContent);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });
});

describe('plugin identification', function () {
    it('returns correct plugin id', function () {
        expect($this->plugin->getId())->toBe('failed-jobs');
    });
});

describe('method chaining', function () {
    it('supports chaining all configuration methods', function () {
        $result = $this->plugin
            ->usingHorizon(true)
            ->hideConnectionOnIndex(true)
            ->hideQueueOnIndex(true)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->authorize(fn () => true)
            ->navigationGroup('System')
            ->navigationLabel('Jobs')
            ->navigationSort(10)
            ->navigationIcon('heroicon-o-queue-list');

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
        expect($result->isUsingHorizon())->toBeTrue();
        expect($result->hideConnectionOnIndex)->toBeTrue();
        expect($result->hideQueueOnIndex)->toBeTrue();
        expect($result->getFiltersLayout())->toBe(FiltersLayout::AboveContent);
        expect($result->isAuthorized())->toBeTrue();
        expect($result->getNavigationGroup())->toBe('System');
        expect($result->getNavigationLabel())->toBe('Jobs');
        expect($result->getNavigationSort())->toBe(10);
        expect($result->getNavigationIcon())->toBe('heroicon-o-queue-list');
    });
});

describe('static factory methods', function () {
    it('make() creates a new plugin instance', function () {
        $plugin = FilamentFailedJobsPlugin::make();

        expect($plugin)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });

    it('get() retrieves registered plugin from filament', function () {
        $plugin = FilamentFailedJobsPlugin::get();

        expect($plugin)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });
});
