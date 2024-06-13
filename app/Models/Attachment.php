<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'path',
        'type',
        'size',
        'notes',
    ];

    protected $casts = [
        'notes' => 'array',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
