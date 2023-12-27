<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorized_users extends Model
{
    use HasFactory;
    protected $primaryKey = 'email';
}
