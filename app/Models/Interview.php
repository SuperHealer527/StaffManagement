<?php

namespace App\Models;

use Database\Factories\InterviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    /** @use HasFactory<InterviewFactory> */
    use HasFactory;
}
