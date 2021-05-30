<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperNote
 */
class Note extends Model
{
    use HasFactory;

    public function noteable()
    {
        return $this->morphTo();
    }

    public static function fromNoteableInstance(string $type, $id)
    {
        return Str::model($type)::findOrFail($id)->notes();
    }
}
