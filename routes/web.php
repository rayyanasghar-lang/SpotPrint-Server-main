<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

use App\Mail\GenericMail;



Route::get('/', function () {
    //here needed to add the url of staging store but it is not deploying yet because of the issues of dns pointing
    return redirect('http://localhost:3002');
});
Route::get('/server', function () {
    return 'server is up';
});

Route::get('/login', function () {
    return redirect('/account');
})->name('login');


// Route::get('/email', function () {
//     $email_data = ['user_name' => 'Irfan Test', 'otp' => '123456',];
//     Mail::to('mi3afzal@gmail.com')->send(new GenericMail('email_verification_otp', $email_data));
// });
