<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'path'
    ];

    public function getFullPathAttribute(): string
    {
        return Storage::url($this->path);
    }
}
