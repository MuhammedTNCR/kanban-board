<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    const TO_DO = 'to_do';
    const IN_PROGRESS = 'in_progress';
    const DONE = 'done';

    const LOW = 'low';
    const MIDDLE = 'middle';
    const HIGH = 'high';

    protected $fillable = [
        'name', 'description', 'status', 'priority', 'assigned_user_id', 'creator_user_id'
    ];

    public static function statuses(): array
    {
        return [
            self::TO_DO,
            self::IN_PROGRESS,
            self::DONE
        ];
    }

    public static function priorities(): array
    {
        return [
            self::LOW,
            self::MIDDLE,
            self::HIGH
        ];
    }

    public function assigned_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }
}
