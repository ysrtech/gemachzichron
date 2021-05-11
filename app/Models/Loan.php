<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperLoan
 */
class Loan extends Model
{
    use HasFactory;

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function dependent()
    {
        return $this->belongsTo(Dependent::class);
    }

    public function guarantors()
    {
        return $this->belongsToMany(Member::class, 'guarantors');
    }

    public function getApplicationCopyAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function setApplicationCopyAttribute(?UploadedFile $file)
    {
        if (!$file) return;

        // delete old application_copy
        if ($this->attributes['application_copy'] ?? false) {
            Storage::delete($this->attributes['application_copy']);
        }

        $this->attributes['application_copy'] = $file->storeAs('applications', "loan_$this->id.{$file->guessExtension()}");
    }
}
