<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'role'
    ];

    protected $hidden = [
        'password',
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('role', function ($query) {
            if(static::class !== User::class) {
                $query->where('role', static::getRole());
            }
        });

        static::creating(function ($model) {
            if (static::class !== User::class) {
                $model->role = static::getRole();
            }
        });
    }

    public static function getRole()
    {
        return null;
    }
}
