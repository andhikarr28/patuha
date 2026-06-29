<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marketplace extends Model
{
    use HasFactory;

    protected $table = 'marketplace';

    protected $fillable = [
        'nama_marketplace',
        'status',
        'last_sync'
    ];
}