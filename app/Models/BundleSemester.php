<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BundleSemester extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function bundle() : BelongsTo
    {
        return $this->belongsTo(Bundle::class , 'bundle_id');
    }

}
