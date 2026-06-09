<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
#[Fillable (['file_type', 'file_path', 'attachable_id', 'attachable_type'])]
class Attachment extends Model
{
    use HasFactory;
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
