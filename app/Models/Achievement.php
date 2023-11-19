<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

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
