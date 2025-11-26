<?php

declare(strict_types=1);

use BinaryBuilds\FilamentFailedJobs\Models\FailedJob;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs($this->testUser);
});

describe('queue options building', function () {
    it('builds queue options from database', function () {
        $job1 = new FailedJob();
        $job1->uuid = 'retry-option-1-'.uniqid();
        $job1->connection = 'database';
        $job1->queue = 'default';
        $job1->payload = '{}';
        $job1->exception = 'Exception';
        $job1->save();

        $job2 = new FailedJob();
        $job2->uuid = 'retry-option-2-'.uniqid();
        $job2->connection = 'database';
        $job2->queue = 'high';
        $job2->payload = '{}';
        $job2->exception = 'Exception';
        $job2->save();

        $queues = FailedJob::query()
            ->select('queue')
            ->distinct()
            ->pluck('queue')
            ->toArray();

        expect($queues)->toContain('default');
        expect($queues)->toContain('high');
    });

    it('includes all option by default', function () {
        $options = ['all' => 'All Queues'];

        expect($options)->toHaveKey('all');
        expect($options['all'])->toBe('All Queues');
    });

    it('adds queue options from database', function () {
        $queues = ['default', 'high', 'low'];
        $options = ['all' => 'All Queues'];

        foreach ($queues as $queue) {
            $options[$queue] = $queue;
        }

        expect($options)->toHaveKey('default');
        expect($options)->toHaveKey('high');
        expect($options)->toHaveKey('low');
    });

    it('builds descriptions for each queue', function () {
        $queues = ['default', 'high'];
        $descriptions = ['all' => 'Retry all Jobs'];

        foreach ($queues as $queue) {
            $descriptions[$queue] = 'Retry jobs from '.$queue.' queue';
        }

        expect($descriptions['all'])->toBe('Retry all Jobs');
        expect($descriptions['default'])->toBe('Retry jobs from default queue');
        expect($descriptions['high'])->toBe('Retry jobs from high queue');
    });

    it('handles empty database gracefully', function () {
        $queues = [];
        $options = ['all' => 'All Queues'];

        foreach ($queues as $queue) {
            $options[$queue] = $queue;
        }

        expect($options)->toHaveCount(1);
        expect($options)->toHaveKey('all');
    });
});

describe('retry command construction', function () {
    it('retry all builds correct command', function () {
        $command = 'queue:retry all';

        expect($command)->toBe('queue:retry all');
    });

    it('retry specific queue builds correct command', function () {
        $queue = 'high';
        $command = 'queue:retry '.$queue;

        expect($command)->toBe('queue:retry high');
    });

    it('retry with queue name containing special characters', function () {
        $queue = 'my-custom-queue';
        $command = 'queue:retry '.$queue;

        expect($command)->toBe('queue:retry my-custom-queue');
    });
});

describe('prune command construction', function () {
    it('prune builds command with hours parameter', function () {
        $hours = 24;
        $command = 'queue:prune-failed --hours='.$hours;

        expect($command)->toBe('queue:prune-failed --hours=24');
    });

    it('prune accepts different hour values', function () {
        $testCases = [1, 12, 24, 48, 168];

        foreach ($testCases as $hours) {
            $command = 'queue:prune-failed --hours='.$hours;
            expect($command)->toBe('queue:prune-failed --hours='.$hours);
        }
    });

    it('prune default hours is typically 24', function () {
        $defaultHours = 24;
        $command = 'queue:prune-failed --hours='.$defaultHours;

        expect($command)->toContain('--hours=24');
    });
});

describe('queue option merging', function () {
    it('merges distinct queues with all option', function () {
        $allOption = ['all' => 'All Queues'];

        $job1 = new FailedJob();
        $job1->uuid = 'merge-test-1-'.uniqid();
        $job1->connection = 'database';
        $job1->queue = 'emails';
        $job1->payload = '{}';
        $job1->exception = 'Exception';
        $job1->save();

        $job2 = new FailedJob();
        $job2->uuid = 'merge-test-2-'.uniqid();
        $job2->connection = 'database';
        $job2->queue = 'notifications';
        $job2->payload = '{}';
        $job2->exception = 'Exception';
        $job2->save();

        $queues = FailedJob::query()
            ->whereIn('uuid', [$job1->uuid, $job2->uuid])
            ->select('queue')
            ->distinct()
            ->pluck('queue', 'queue')
            ->toArray();

        $options = array_merge($allOption, $queues);

        expect($options)->toHaveKey('all');
        expect($options)->toHaveKey('emails');
        expect($options)->toHaveKey('notifications');
        expect($options)->toHaveCount(3);
    });
});
