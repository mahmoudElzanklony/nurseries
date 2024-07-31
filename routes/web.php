<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoonPaymentController;




Route::get('/noon_payment',[NoonPaymentController::class,'index']);
Route::get('/noon_payment_response',[NoonPaymentController::class,'response']);



