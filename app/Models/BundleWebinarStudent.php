<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleWebinarStudent extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class , 'user_id');
    }
    public function bundleWebinar() : BelongsTo
    {
        return $this->belongsTo(BundleWebinar::class , 'bundle_webinar_id');
    }
}
