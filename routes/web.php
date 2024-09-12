<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoonPaymentController;




Route::get('/noon_payment',[NoonPaymentController::class,'index']);
Route::get('/noon_payment_response',[NoonPaymentController::class,'response'])->name('noon.payment.response');
Route::get('/noon_payment_response_failure',[NoonPaymentController::class,'failure'])->name('noon.payment.failure');



