<?php

namespace App\Models;

use Database\Factories\TranslationFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id', 'created_at', 'updated_at')]
class Translation extends Model
{
    /** @use HasFactory<TranslationFactory> */
    use HasFactory;

    public function translatable()
    {
        return $this->morphTo();
    }
}
