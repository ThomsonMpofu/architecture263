<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanApplicationMarkup extends Model
{
    public const TYPE_PIN = 'pin';

    public const TYPE_LINE = 'line';

    protected $fillable = [
        'plan_application_id',
        'user_id',
        'type',
        'page',
        'drawing_version',
        'x',
        'y',
        'x2',
        'y2',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'page' => 'integer',
            'drawing_version' => 'integer',
            'x' => 'float',
            'y' => 'float',
            'x2' => 'float',
            'y2' => 'float',
        ];
    }

    public function planApplication(): BelongsTo
    {
        return $this->belongsTo(PlanApplication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
