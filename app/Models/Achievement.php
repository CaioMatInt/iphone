<?php

namespace App\Models;

use App\Events\AchievementUnlocked;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    const LESSON_TYPE = 'lesson';
    const COMMENT_TYPE = 'comment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'threshold',
        'order',
    ];

    /**
     * The users that have the achievement.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
