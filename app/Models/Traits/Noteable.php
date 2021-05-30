<?php

namespace App\Models\Traits;

use App\Models\Note;

trait Noteable
{
    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
