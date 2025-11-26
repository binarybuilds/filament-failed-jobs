<?php

declare(strict_types=1);

use BinaryBuilds\FilamentFailedJobs\Resources\FailedJobs\FailedJobResource;
use Filament\Support\Icons\Heroicon;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->plugin = filament('failed-jobs');

    actingAs($this->testUser);
});

it('renders the failed jobs resource with default settings', function () {
    $this->get(FailedJobResource::getUrl())
        ->assertSuccessful();
})->skip('Requires full Livewire test environment setup');

it('only gives access to authorized users', function () {
    $this->plugin->authorize(fn () => false);

    $this->get(FailedJobResource::getUrl())
        ->assertForbidden();
});

it('allows access when authorize returns true', function () {
    $this->plugin->authorize(fn () => true);

    $this->get(FailedJobResource::getUrl())
        ->assertSuccessful();
})->skip('Requires full Livewire test environment setup');

it('allows customization of navigation group', function () {
    $this->plugin->navigationGroup('System');

    expect(FailedJobResource::getNavigationGroup())->toBe('System');
});

it('allows customization of navigation label', function () {
    $this->plugin->navigationLabel('Custom Label');

    expect(FailedJobResource::getNavigationLabel())->toBe('Custom Label');
});

it('returns default navigation label when not customized', function () {
    expect(FailedJobResource::getNavigationLabel())->toBe('Failed Jobs');
});

it('allows customization of navigation sort', function () {
    $this->plugin->navigationSort(10);

    expect(FailedJobResource::getNavigationSort())->toBe(10);
});

it('returns default navigation sort when not customized', function () {
    expect(FailedJobResource::getNavigationSort())->toBe(9999);
});

it('allows customization of navigation icon as string', function () {
    $this->plugin->navigationIcon('heroicon-o-exclamation-triangle');

    expect(FailedJobResource::getNavigationIcon())->toBe('heroicon-o-exclamation-triangle');
});

it('allows customization of navigation icon as Heroicon enum', function () {
    $this->plugin->navigationIcon(Heroicon::ExclamationTriangle);

    expect(FailedJobResource::getNavigationIcon())->toBe(Heroicon::ExclamationTriangle);
});

it('returns default navigation icon when not customized', function () {
    expect(FailedJobResource::getNavigationIcon())->toBe(Heroicon::QueueList);
});

it('supports closure for authorization', function () {
    $this->plugin->authorize(fn () => auth()->user()->role === 'admin');

    $this->get(FailedJobResource::getUrl())
        ->assertForbidden();
});

it('supports closure for navigation group', function () {
    $this->plugin->navigationGroup(fn () => 'Dynamic Group');

    expect(FailedJobResource::getNavigationGroup())->toBe('Dynamic Group');
});

it('supports closure for navigation label', function () {
    $this->plugin->navigationLabel(fn () => 'Dynamic Label');

    expect(FailedJobResource::getNavigationLabel())->toBe('Dynamic Label');
});

it('supports closure for navigation sort', function () {
    $this->plugin->navigationSort(fn () => 42);

    expect(FailedJobResource::getNavigationSort())->toBe(42);
});

it('supports closure for navigation icon', function () {
    $this->plugin->navigationIcon(fn () => 'heroicon-o-cog');

    expect(FailedJobResource::getNavigationIcon())->toBe('heroicon-o-cog');
});
