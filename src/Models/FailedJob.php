<?php

namespace BinaryBuilds\FilamentFailedJobs\Models;

use Illuminate\Database\Eloquent\Model;

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
