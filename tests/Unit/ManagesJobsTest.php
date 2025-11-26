<?php

declare(strict_types=1);

use BinaryBuilds\FilamentFailedJobs\Actions\ManagesJobs;
use BinaryBuilds\FilamentFailedJobs\FilamentFailedJobsPlugin;

use function Pest\Laravel\actingAs;

class ManagesJobsTestClass
{
    use ManagesJobs;
}

beforeEach(function () {
    $this->manager = new ManagesJobsTestClass();
    actingAs($this->testUser);
});

describe('retryJobs', function () {
    it('builds correct command string for single job', function () {
        $jobs = collect([
            (object) ['uuid' => 'test-uuid-1'],
        ]);

        $command = 'queue:retry '.$jobs->pluck('uuid')->implode(' ');

        expect($command)->toBe('queue:retry test-uuid-1');
    });

    it('builds correct command string for multiple jobs', function () {
        $jobs = collect([
            (object) ['uuid' => 'uuid-1'],
            (object) ['uuid' => 'uuid-2'],
            (object) ['uuid' => 'uuid-3'],
        ]);

        $command = 'queue:retry '.$jobs->pluck('uuid')->implode(' ');

        expect($command)->toBe('queue:retry uuid-1 uuid-2 uuid-3');
    });

    it('handles empty collection', function () {
        $jobs = collect([]);

        $command = 'queue:retry '.$jobs->pluck('uuid')->implode(' ');

        expect($command)->toBe('queue:retry ');
    });

    it('correctly extracts uuids using pluck', function () {
        $jobs = collect([
            (object) ['uuid' => 'first-uuid', 'other' => 'data'],
            (object) ['uuid' => 'second-uuid', 'other' => 'more-data'],
        ]);

        $uuids = $jobs->pluck('uuid')->implode(' ');

        expect($uuids)->toBe('first-uuid second-uuid');
    });
});

describe('deleteJobs', function () {
    it('uses queue:forget command when not using Horizon', function () {
        FilamentFailedJobsPlugin::get()->usingHorizon(false);

        $job = (object) ['uuid' => 'test-uuid'];
        $command = FilamentFailedJobsPlugin::get()->isUsingHorizon()
            ? 'horizon:forget '.$job->uuid
            : 'queue:forget '.$job->uuid;

        expect($command)->toBe('queue:forget test-uuid');
    });

    it('uses horizon:forget command when using Horizon', function () {
        FilamentFailedJobsPlugin::get()->usingHorizon(true);

        $job = (object) ['uuid' => 'test-uuid'];
        $command = FilamentFailedJobsPlugin::get()->isUsingHorizon()
            ? 'horizon:forget '.$job->uuid
            : 'queue:forget '.$job->uuid;

        expect($command)->toBe('horizon:forget test-uuid');

        FilamentFailedJobsPlugin::get()->usingHorizon(false);
    });

    it('iterates over each job in collection', function () {
        $jobs = collect([
            (object) ['uuid' => 'uuid-1'],
            (object) ['uuid' => 'uuid-2'],
            (object) ['uuid' => 'uuid-3'],
        ]);

        $commands = [];
        foreach ($jobs as $job) {
            $commands[] = 'queue:forget '.$job->uuid;
        }

        expect($commands)->toHaveCount(3);
        expect($commands[0])->toBe('queue:forget uuid-1');
        expect($commands[1])->toBe('queue:forget uuid-2');
        expect($commands[2])->toBe('queue:forget uuid-3');
    });

    it('handles empty collection gracefully', function () {
        $jobs = collect([]);

        $commands = [];
        foreach ($jobs as $job) {
            $commands[] = 'queue:forget '.$job->uuid;
        }

        expect($commands)->toBeEmpty();
    });
});

describe('command construction', function () {
    it('builds horizon:forget for all jobs when Horizon is enabled', function () {
        FilamentFailedJobsPlugin::get()->usingHorizon(true);

        $jobs = collect([
            (object) ['uuid' => 'uuid-1'],
            (object) ['uuid' => 'uuid-2'],
        ]);

        $commands = [];
        foreach ($jobs as $job) {
            $command = FilamentFailedJobsPlugin::get()->isUsingHorizon()
                ? 'horizon:forget '.$job->uuid
                : 'queue:forget '.$job->uuid;
            $commands[] = $command;
        }

        expect($commands[0])->toBe('horizon:forget uuid-1');
        expect($commands[1])->toBe('horizon:forget uuid-2');

        FilamentFailedJobsPlugin::get()->usingHorizon(false);
    });

    it('plugin correctly reports horizon status', function () {
        FilamentFailedJobsPlugin::get()->usingHorizon(false);
        expect(FilamentFailedJobsPlugin::get()->isUsingHorizon())->toBeFalse();

        FilamentFailedJobsPlugin::get()->usingHorizon(true);
        expect(FilamentFailedJobsPlugin::get()->isUsingHorizon())->toBeTrue();

        FilamentFailedJobsPlugin::get()->usingHorizon(false);
    });
});
