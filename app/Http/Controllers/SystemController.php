<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System;

class SystemController extends Controller
{
    public function get($id){
        $system = System::find($id);
        if (!$system) return $this->errorResponse('', 'Setting not found', 404);
        return $this->successResponse($system, 'Success', 200);
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'system' => 'required|array',
        ]);

        $system = System::find($id);
        if (!$system) return $this->errorResponse('', 'Setting not found', 404);

        System::where('id', $id)->update($validated['system']);
        return $this->successResponse(null, 'Setting Saved successfully', 200);
    }


    // for FrontEnd
    public function getConfigarations($id){
        $system = System::select('configurations', 'global_product_options')->find($id);
        if (!$system) return $this->errorResponse('', 'Configarations not found', 404);
        return $this->successResponse($system, 'Success', 200);
    }
}