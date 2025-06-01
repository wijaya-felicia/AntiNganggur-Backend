<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class Application extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'applications';
    protected $table = 'applications';

    protected $fillable = [
        'employee_ids',
        'job_id',
    ];

    public function Employee()
    {
        return $this->belongsTo(Employee::class, "employee_ids");
    }

    public function Job()
    {
        return $this->belongsTo(Job::class, "job_id");
    }
}
