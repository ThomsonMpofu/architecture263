<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
