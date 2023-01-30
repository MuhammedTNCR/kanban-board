<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use \Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function my_tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    public function created_tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'creator_user_id');
    }

    public function getAllTasksAttribute()
    {
        return Task::query()->where('creator_user_id', $this->id)->orWhere('assigned_user_id')
            ->groupBy('status')->orderBy('order')->get();
    }
}
