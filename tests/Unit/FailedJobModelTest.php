<?php

declare(strict_types=1);

use BinaryBuilds\FilamentFailedJobs\Models\FailedJob;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs($this->testUser);
});

describe('FailedJob model', function () {
    it('can be instantiated', function () {
        $model = new FailedJob();

        expect($model)->toBeInstanceOf(FailedJob::class);
    });

    it('uses table name from config', function () {
        config(['queue.failed.table' => 'failed_jobs']);

        $model = new FailedJob();

        expect($model->getTable())->toBe('failed_jobs');
    });

    it('respects custom table name in config', function () {
        config(['queue.failed.table' => 'custom_failed_jobs']);

        $model = new FailedJob();

        expect($model->getTable())->toBe('custom_failed_jobs');

        config(['queue.failed.table' => 'failed_jobs']);
    });

    it('casts payload to string', function () {
        $model = new FailedJob();

        expect($model->getCasts())->toHaveKey('payload');
        expect($model->getCasts()['payload'])->toBe('string');
    });
});

describe('FailedJob database operations', function () {
    it('can create a failed job record', function () {
        $job = new FailedJob();
        $job->uuid = 'test-uuid-'.uniqid();
        $job->connection = 'database';
        $job->queue = 'default';
        $job->payload = json_encode(['displayName' => 'App\\Jobs\\TestJob']);
        $job->exception = 'Test exception message';
        $job->save();

        expect($job)->toBeInstanceOf(FailedJob::class);
        expect($job->exists)->toBeTrue();
    });

    it('can query failed jobs', function () {
        $job = new FailedJob();
        $job->uuid = 'query-test-uuid-'.uniqid();
        $job->connection = 'database';
        $job->queue = 'default';
        $job->payload = json_encode(['displayName' => 'App\\Jobs\\QueryTestJob']);
        $job->exception = 'Query test exception';
        $job->save();

        $jobs = FailedJob::all();

        expect($jobs)->not->toBeEmpty();
    });

    it('can find a failed job by uuid', function () {
        $uuid = 'find-test-uuid-'.uniqid();

        $job = new FailedJob();
        $job->uuid = $uuid;
        $job->connection = 'redis';
        $job->queue = 'high';
        $job->payload = json_encode(['displayName' => 'App\\Jobs\\FindTestJob']);
        $job->exception = 'Find test exception';
        $job->save();

        $foundJob = FailedJob::where('uuid', $uuid)->first();

        expect($foundJob)->not->toBeNull();
        expect($foundJob->uuid)->toBe($uuid);
        expect($foundJob->connection)->toBe('redis');
        expect($foundJob->queue)->toBe('high');
    });

    it('can delete a failed job', function () {
        $uuid = 'delete-test-uuid-'.uniqid();

        $job = new FailedJob();
        $job->uuid = $uuid;
        $job->connection = 'database';
        $job->queue = 'default';
        $job->payload = json_encode(['displayName' => 'App\\Jobs\\DeleteTestJob']);
        $job->exception = 'Delete test exception';
        $job->save();

        $job->delete();

        expect(FailedJob::where('uuid', $uuid)->exists())->toBeFalse();
    });

    it('stores and retrieves payload as string', function () {
        $uuid = 'payload-test-uuid-'.uniqid();
        $payloadData = ['displayName' => 'App\\Jobs\\PayloadTestJob', 'data' => ['key' => 'value']];

        $job = new FailedJob();
        $job->uuid = $uuid;
        $job->connection = 'database';
        $job->queue = 'default';
        $job->payload = json_encode($payloadData);
        $job->exception = 'Payload test exception';
        $job->save();

        $foundJob = FailedJob::where('uuid', $uuid)->first();

        expect($foundJob->payload)->toBeString();
        expect(json_decode($foundJob->payload, true))->toBe($payloadData);
    });

    it('can filter jobs by connection', function () {
        $uuid1 = 'conn-test-1-'.uniqid();
        $uuid2 = 'conn-test-2-'.uniqid();

        $job1 = new FailedJob();
        $job1->uuid = $uuid1;
        $job1->connection = 'database';
        $job1->queue = 'default';
        $job1->payload = json_encode(['displayName' => 'Job1']);
        $job1->exception = 'Exception 1';
        $job1->save();

        $job2 = new FailedJob();
        $job2->uuid = $uuid2;
        $job2->connection = 'redis';
        $job2->queue = 'default';
        $job2->payload = json_encode(['displayName' => 'Job2']);
        $job2->exception = 'Exception 2';
        $job2->save();

        $databaseJobs = FailedJob::where('connection', 'database')
            ->whereIn('uuid', [$uuid1, $uuid2])
            ->get();

        expect($databaseJobs)->toHaveCount(1);
        expect($databaseJobs->first()->uuid)->toBe($uuid1);
    });

    it('can filter jobs by queue', function () {
        $uuid1 = 'queue-test-1-'.uniqid();
        $uuid2 = 'queue-test-2-'.uniqid();

        $job1 = new FailedJob();
        $job1->uuid = $uuid1;
        $job1->connection = 'database';
        $job1->queue = 'high';
        $job1->payload = json_encode(['displayName' => 'HighPriorityJob']);
        $job1->exception = 'High priority exception';
        $job1->save();

        $job2 = new FailedJob();
        $job2->uuid = $uuid2;
        $job2->connection = 'database';
        $job2->queue = 'low';
        $job2->payload = json_encode(['displayName' => 'LowPriorityJob']);
        $job2->exception = 'Low priority exception';
        $job2->save();

        $highPriorityJobs = FailedJob::where('queue', 'high')
            ->whereIn('uuid', [$uuid1, $uuid2])
            ->get();

        expect($highPriorityJobs)->toHaveCount(1);
        expect($highPriorityJobs->first()->queue)->toBe('high');
    });

    it('can get distinct connections', function () {
        $uuid1 = 'distinct-conn-1-'.uniqid();
        $uuid2 = 'distinct-conn-2-'.uniqid();
        $uuid3 = 'distinct-conn-3-'.uniqid();

        $job1 = new FailedJob();
        $job1->uuid = $uuid1;
        $job1->connection = 'database';
        $job1->queue = 'default';
        $job1->payload = '{}';
        $job1->exception = 'Exception';
        $job1->save();

        $job2 = new FailedJob();
        $job2->uuid = $uuid2;
        $job2->connection = 'redis';
        $job2->queue = 'default';
        $job2->payload = '{}';
        $job2->exception = 'Exception';
        $job2->save();

        $job3 = new FailedJob();
        $job3->uuid = $uuid3;
        $job3->connection = 'database';
        $job3->queue = 'default';
        $job3->payload = '{}';
        $job3->exception = 'Exception';
        $job3->save();

        $connections = FailedJob::whereIn('uuid', [$uuid1, $uuid2, $uuid3])
            ->distinct()
            ->pluck('connection')
            ->toArray();

        expect($connections)->toContain('database');
        expect($connections)->toContain('redis');
    });
});
