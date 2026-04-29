<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\ChatbotController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email-otp', [AuthController::class, 'verifyEmailOtp']);
Route::post('/resend-email-otp', [AuthController::class, 'resendEmailOtp']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendForgotPasswordOtp']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyForgotPasswordOtp']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPasswordWithOtp']);
Route::post('/webhook', [WebhookController::class, 'handle']);

Route::get('/courses', [CourseController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/courses/{courseId}', [CourseController::class, 'show']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/enroll', [EnrollmentController::class, 'enroll']);
    Route::patch('/enroll/{enrollmentId}/progress', [EnrollmentController::class, 'updateProgress']);
    Route::get('/my-enrollments', [EnrollmentController::class, 'myEnrollments']);
    Route::get('/enroll/{enrollmentId}/certificate', [EnrollmentController::class, 'certificate']);
    Route::post('/enroll/{enrollmentId}/certificate/generate', [EnrollmentController::class, 'generateCertificate']);

    Route::post('/cart', [CartController::class, 'store']);
    Route::get('/cart', [CartController::class, 'show']);

    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'index']);

    Route::get('/recommendations', [RecommendationController::class, 'index']);
    Route::post('/chatbot/message', [ChatbotController::class, 'message']);
    Route::get('/chatbot/history', [ChatbotController::class, 'history']);
    Route::delete('/chatbot/history', [ChatbotController::class, 'clearHistory']);

    Route::post('/analytics/track', [AnalyticsController::class, 'track']);
    Route::get('/analytics/insights', [AnalyticsController::class, 'insights']);

    Route::get('/profile', [ProfileController::class, 'me']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::patch('/profile/password', [ProfileController::class, 'changePassword']);
});
