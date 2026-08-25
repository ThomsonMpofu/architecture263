<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Engagement extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUS_PENDING_ARCHITECT_APPROVAL = 'pending_architect_approval';

    public const STATUS_ARCHITECT_APPROVED = 'architect_approved';

    public const STATUS_CONTRACT_SIGNED = 'contract_signed';

    protected $fillable = [
        'client_id',
        'architect_id',
        'status',
        'architect_approved_at',
        'client_signed_at',
        'architect_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'architect_approved_at' => 'datetime',
            'client_signed_at' => 'datetime',
            'architect_signed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function architect(): BelongsTo
    {
        return $this->belongsTo(User::class, 'architect_id');
    }
}
