<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Mail\SamplePackRequestMail;


class SamplePackController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $validationRules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        // Send the email
        Mail::to(['info@spotprint.co.uk', 'mi3afzal@gmail.com'])->send(new SamplePackRequestMail($validated));

        return $this->successResponse('', 'Sample pack request submitted successfully.', 200);
    }
}
