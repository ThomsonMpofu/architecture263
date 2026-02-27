<?php

namespace App\Http\Controllers;

use App\Models\PlanApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanApplicationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_no' => 'required|string|unique:plan_applications,plan_no',
            'stand_no' => 'required|string',
            'postal_address' => 'required|string',
            'estimated_cost' => 'required|numeric',
            'purpose' => 'required|string',
            'industry_type' => 'nullable|string',
            'project_type' => 'required|string',
            
            // Owner
            'owner_name' => 'required|string',
            'owner_address' => 'required|string',
            'owner_phone' => 'nullable|string',
            
            // Professionals
            'architect_name' => 'nullable|string',
            'architect_address' => 'nullable|string',
            'architect_phone' => 'nullable|string',
            'contractor_name' => 'nullable|string',
            'contractor_address' => 'nullable|string',
            'contractor_phone' => 'nullable|string',
            'supervision' => 'required|string',
            
            // Dimensions
            'area_ground_floor' => 'required|numeric',
            'area_first_floor' => 'nullable|numeric',
            'area_total' => 'required|numeric',
            'area_outbuildings' => 'nullable|numeric',
            'fire_fighting_equipment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        
        // Assign user_id from authenticated user
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'pending'; // Default status

        $application = PlanApplication::create($data);

        return response()->json([
            'message' => 'Plan application submitted successfully.',
            'data' => $application,
        ], 201);
    }
}
