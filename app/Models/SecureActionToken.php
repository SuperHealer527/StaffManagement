<?php

namespace App\Models;

use Database\Factories\SecureActionTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecureActionToken extends Model
{
    /** @use HasFactory<SecureActionTokenFactory> */
    use HasFactory;
}
