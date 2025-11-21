<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Mail\ContactUsMail;


class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $validationRules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:1000',
        ];
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails())
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        // Send the email
        Mail::to(['info@spotprint.co.uk', 'muhammadmomin2602@gmail.com'])->send(new ContactUsMail($validated));

        return $this->successResponse('', 'Message submit successfully', 200);
    }
}
