<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class File extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'files';
    protected $table = 'files';

    protected $fillable = [
        'user_id',
        'type',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
