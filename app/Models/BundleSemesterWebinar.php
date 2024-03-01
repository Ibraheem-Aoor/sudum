<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleSemesterWebinar extends Model
{
    use HasFactory;

    public function semester() : BelongsTo
    {
        return $this->belongsTo(BundleSemester::class , 'bundle_semester_id');
    }
    public function webinar() : BelongsTo
    {
        return $this->belongsTo(Webinar::class , 'webinar_id');
    }

}
