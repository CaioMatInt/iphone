<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'password' => 'hashed',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\UserCreated::class,
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * Count of lessons that a user has watched.
     */
    public function lessonProgress()
    {
        return $this->hasOne(UserLessonProgress::class);
    }

    /**
     * Count of comments that a user has made.
     */
    public function commentProgress()
    {
        return $this->hasOne(UserCommentProgress::class);
    }

    /**
     * The achievements that the user has earned.
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievement')->withTimestamps();
    }

    /**
     * The badges that the user has earned.
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badge')->withTimestamps();
    }
}

