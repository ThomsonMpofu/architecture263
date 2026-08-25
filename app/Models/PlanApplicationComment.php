<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanApplicationComment extends Model
{
    protected $fillable = [
        'plan_application_id',
        'user_id',
        'body',
    ];

    public function planApplication(): BelongsTo
    {
        return $this->belongsTo(PlanApplication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
