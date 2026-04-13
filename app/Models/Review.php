<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id', 'created_at', 'updated_at')]
class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;
}
