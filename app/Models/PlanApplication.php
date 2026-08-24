<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PlanApplication extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_REVISION_REQUESTED = 'revision_requested';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'engagement_id',
        'submitted_by',
        'council_reviewer_id',
        'plan_no',
        'stand_no',
        'postal_address',
        'estimated_cost',
        'purpose',
        'industry_type',
        'project_type',
        'owner_name',
        'owner_address',
        'owner_phone',
        'architect_name',
        'architect_address',
        'architect_phone',
        'contractor_name',
        'contractor_address',
        'contractor_phone',
        'supervision',
        'area_ground_floor',
        'area_first_floor',
        'area_total',
        'area_outbuildings',
        'fire_fighting_equipment',
        'drawings_path',
        'drawings_original_name',
        'drawings_version',
        'status',
    ];

    public function engagement(): BelongsTo
    {
        return $this->belongsTo(Engagement::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function councilReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'council_reviewer_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PlanApplicationComment::class);
    }

    public function markups(): HasMany
    {
        return $this->hasMany(PlanApplicationMarkup::class)->oldest();
    }

    /**
     * Only the markups placed on the *current* drawing version — old
     * corrections stay in the database as a record, but don't carry over
     * onto a newer, different drawing.
     */
    public function currentMarkups(): HasMany
    {
        return $this->markups()->where('drawing_version', $this->drawings_version);
    }

    /**
     * Every drawing ever uploaded for this application, oldest first —
     * uploading a new one never deletes or overwrites an earlier version.
     */
    public function drawings(): HasMany
    {
        return $this->hasMany(PlanApplicationDrawing::class)->oldest('version');
    }
}
