<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['id', 'user_id', 'ip_address', 'user_agent', 'playload', 'last_activity'];
}
