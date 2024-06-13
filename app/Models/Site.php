<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'address',
        'city',
        'state',
        'zip',
        'badges',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'notes' => 'array',
        'badges' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class);
    }

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function getBadgesAttribute()
    {
        return $this->todos->pluck('badges')->flatten()->unique();
    }

    public function getTagsAttribute()
    {
        return $this->todos->pluck('tags')->flatten()->unique();
    }

    

}
