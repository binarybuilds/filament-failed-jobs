<?php

declare(strict_types=1);

use BinaryBuilds\FilamentFailedJobs\FilamentFailedJobsPlugin;
use Filament\Tables\Enums\FiltersLayout;

beforeEach(function () {
    $plugin = FilamentFailedJobsPlugin::get();
    $plugin->hideConnectionOnIndex = false;
    $plugin->hideQueueOnIndex = false;
    $plugin->filtersLayout(FiltersLayout::Dropdown);
});

describe('plugin column visibility settings', function () {
    it('shows connection column by default', function () {
        expect(FilamentFailedJobsPlugin::get()->hideConnectionOnIndex)->toBeFalse();
    });

    it('hides connection column when configured', function () {
        FilamentFailedJobsPlugin::get()->hideConnectionOnIndex(true);

        expect(FilamentFailedJobsPlugin::get()->hideConnectionOnIndex)->toBeTrue();
    });

    it('shows queue column by default', function () {
        expect(FilamentFailedJobsPlugin::get()->hideQueueOnIndex)->toBeFalse();
    });

    it('hides queue column when configured', function () {
        FilamentFailedJobsPlugin::get()->hideQueueOnIndex(true);

        expect(FilamentFailedJobsPlugin::get()->hideQueueOnIndex)->toBeTrue();
    });

    it('can hide both connection and queue columns', function () {
        FilamentFailedJobsPlugin::get()->hideConnectionOnIndex(true);
        FilamentFailedJobsPlugin::get()->hideQueueOnIndex(true);

        expect(FilamentFailedJobsPlugin::get()->hideConnectionOnIndex)->toBeTrue();
        expect(FilamentFailedJobsPlugin::get()->hideQueueOnIndex)->toBeTrue();
    });

    it('can show both connection and queue columns', function () {
        FilamentFailedJobsPlugin::get()->hideConnectionOnIndex(false);
        FilamentFailedJobsPlugin::get()->hideQueueOnIndex(false);

        expect(FilamentFailedJobsPlugin::get()->hideConnectionOnIndex)->toBeFalse();
        expect(FilamentFailedJobsPlugin::get()->hideQueueOnIndex)->toBeFalse();
    });
});

describe('filters layout configuration', function () {
    it('defaults to dropdown layout', function () {
        expect(FilamentFailedJobsPlugin::get()->getFiltersLayout())->toBe(FiltersLayout::Dropdown);
    });

    it('can configure above content layout', function () {
        FilamentFailedJobsPlugin::get()->filtersLayout(FiltersLayout::AboveContent);

        expect(FilamentFailedJobsPlugin::get()->getFiltersLayout())->toBe(FiltersLayout::AboveContent);
    });

    it('can configure above content collapsible layout', function () {
        FilamentFailedJobsPlugin::get()->filtersLayout(FiltersLayout::AboveContentCollapsible);

        expect(FilamentFailedJobsPlugin::get()->getFiltersLayout())->toBe(FiltersLayout::AboveContentCollapsible);
    });

    it('can configure below content layout', function () {
        FilamentFailedJobsPlugin::get()->filtersLayout(FiltersLayout::BelowContent);

        expect(FilamentFailedJobsPlugin::get()->getFiltersLayout())->toBe(FiltersLayout::BelowContent);
    });
});

describe('horizon configuration', function () {
    it('defaults to not using Horizon', function () {
        FilamentFailedJobsPlugin::get()->usingHorizon(false);

        expect(FilamentFailedJobsPlugin::get()->isUsingHorizon())->toBeFalse();
    });

    it('can enable Horizon mode', function () {
        FilamentFailedJobsPlugin::get()->usingHorizon(true);

        expect(FilamentFailedJobsPlugin::get()->isUsingHorizon())->toBeTrue();

        FilamentFailedJobsPlugin::get()->usingHorizon(false);
    });
});

describe('fluent api', function () {
    it('hideConnectionOnIndex returns plugin instance', function () {
        $result = FilamentFailedJobsPlugin::get()->hideConnectionOnIndex(true);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });

    it('hideQueueOnIndex returns plugin instance', function () {
        $result = FilamentFailedJobsPlugin::get()->hideQueueOnIndex(true);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });

    it('filtersLayout returns plugin instance', function () {
        $result = FilamentFailedJobsPlugin::get()->filtersLayout(FiltersLayout::Dropdown);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });

    it('usingHorizon returns plugin instance', function () {
        $result = FilamentFailedJobsPlugin::get()->usingHorizon(false);

        expect($result)->toBeInstanceOf(FilamentFailedJobsPlugin::class);
    });
});
