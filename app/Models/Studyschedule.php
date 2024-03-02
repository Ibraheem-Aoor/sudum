<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Studyschedule extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = [
        'bundle',
        'webinar',
    ];

    public function webinar() : BelongsTo
    {
        return $this->belongsTo(Webinar::class , 'webinar_id');
    }
    public function bundle() : BelongsTo
    {
        return $this->belongsTo(Bundle::class , 'bundle_id');
    }
}
