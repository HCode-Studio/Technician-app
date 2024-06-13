<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'completed',
        'user_id',
        'site_id',
        'badges',
        'tags',
        'created_at',
        "updated_at"
    ];

    protected $casts = [
        'badges' => 'array',
        'tags' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

}
