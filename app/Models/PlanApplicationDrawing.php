<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanApplicationDrawing extends Model
{
    protected $fillable = [
        'plan_application_id',
        'uploaded_by',
        'version',
        'path',
        'original_name',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
        ];
    }

    public function planApplication(): BelongsTo
    {
        return $this->belongsTo(PlanApplication::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
