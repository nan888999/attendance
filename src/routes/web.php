<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MailSendController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// 認証が必要なルート
Route::middleware('auth')->group(function() {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');
});

// ログインページとログイン処理
Route::get('/login', [UserController::class, 'viewLogin'])->name('login');
Route::post('/login', [UserController::class, 'login']);

// 登録ページと登録処理
Route::get('/register', [UserController::class, 'viewRegister']);
Route::post('/register', [UserController::class, 'register']);

// メール認証ページとメール認証処理
Route::get('/verify_email', [UserController::class, 'viewVerifyEmail']);
Route::post('/verify_email', [UserController::class, 'verifyEmail']);

// 認証メール内リンククリック時の処理
Route::get('/email/verify/{id}/{hash}', [UserController::class, 'emailVerified'])->middleware('signed')->name('verification.verify');

// 認証メールの再送信
Route::get('/resend', [UserController::class,'viewResendForm'])->name('resend');
Route::post('/resend', [UserController::class, 'resend']);

// 勤怠管理一覧表示
Route::get('/attendance', [AttendanceController::class, 'attendance'])->name('attendance');

// 勤怠管理
Route::post('/work_start', [AttendanceController::class, 'workStart'])->middleware('auth');
Route::post('/work_end', [AttendanceController::class,'workEnd'])->middleware('auth');
Route::post('/break_start', [AttendanceController::class, 'breakStart'])->middleware('auth');
Route::post('/break_end', [AttendanceController::class,'breakEnd'])->middleware('auth');

Route::get('/change_date', [AttendanceController::class, 'changeDate']);

// ユーザー別勤怠表示
Route::get('/user_profile/{user_id}', [AttendanceController::class, 'showProfile'])->name('user.profile');
