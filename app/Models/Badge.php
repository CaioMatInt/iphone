<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'achievement_threshold',
        'order',
    ];

    /**
     * Users that have the badge.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
