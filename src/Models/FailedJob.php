<?php

namespace BinaryBuilds\FilamentFailedJobs\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $job
 * @property string $payload
 */
class FailedJob extends Model
{
    public $timestamps = false;

    protected $casts = [
        'payload' => 'string',
    ];

    public function getTable()
    {
        return config('queue.failed.table');
    }
}
