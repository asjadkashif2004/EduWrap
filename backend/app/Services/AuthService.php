<?php

namespace App\Services;

use App\Models\EmailVerificationOtp;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthService
{
    public function register(array $validated): array
    {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $otpDispatched = $this->sendEmailVerificationOtpSafely($user, 'register');

        return [
            'message' => $otpDispatched
                ? 'A 6-digit OTP has been sent to your email. Verify your account to continue.'
                : 'Account created. Verification OTP could not be sent right now; please tap resend OTP in the app.',
            'requires_verification' => true,
            'email' => $user->email,
        ];
    }

    public function login(array $validated): ?array
    {
        $user = User::query()->where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return null;
        }

        if (!$user->email_verified_at) {
            $otpDispatched = $this->sendEmailVerificationOtpSafely($user, 'login');
            return [
                'message' => $otpDispatched
                    ? 'Please verify your email using the OTP we sent.'
                    : 'Your account is not verified yet. OTP delivery is temporarily unavailable; please try resend OTP shortly.',
                'requires_verification' => true,
                'email' => $user->email,
            ];
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return compact('user', 'token');
    }

    public function verifyEmailOtp(array $validated): array
    {
        $user = User::query()->where('email', $validated['email'])->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email.'],
            ]);
        }

        if ($user->email_verified_at) {
            $token = $user->createToken('mobile')->plainTextToken;
            return [
                'message' => 'Email already verified.',
                'user' => $user,
                'token' => $token,
            ];
        }

        $otpRecord = EmailVerificationOtp::query()->where('user_id', $user->id)->first();
        if (!$otpRecord || $otpRecord->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => ['OTP expired. Please request a new code.'],
            ]);
        }

        if (!Hash::check($validated['otp'], $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');
            if ($otpRecord->attempts >= 5) {
                $otpRecord->delete();
                throw ValidationException::withMessages([
                    'otp' => ['Too many invalid attempts. Request a new OTP.'],
                ]);
            }

            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP code.'],
            ]);
        }

        $user->forceFill(['email_verified_at' => Carbon::now()])->save();
        $otpRecord->delete();
        $token = $user->createToken('mobile')->plainTextToken;

        return [
            'message' => 'Email verified successfully.',
            'user' => $user,
            'token' => $token,
        ];
    }

    public function resendEmailOtp(string $email): array
    {
        $user = User::query()->where('email', $email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email.'],
            ]);
        }

        if ($user->email_verified_at) {
            return ['message' => 'Email is already verified.'];
        }

        $otpDispatched = $this->sendEmailVerificationOtpSafely($user, 'resend-email-otp');
        return [
            'message' => $otpDispatched
                ? 'A new OTP has been sent to your email.'
                : 'Unable to send OTP right now. Please try again in a minute.',
        ];
    }

    public function sendForgotPasswordOtp(string $email): array
    {
        $user = User::query()->where('email', $email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email.'],
            ]);
        }

        $otpRecord = PasswordResetOtp::query()->where('email', $email)->first();
        if ($otpRecord?->last_sent_at && $otpRecord->last_sent_at->greaterThan(Carbon::now()->subSeconds(60))) {
            throw ValidationException::withMessages([
                'email' => ['Please wait a minute before requesting another OTP.'],
            ]);
        }

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::query()->updateOrCreate(
            ['email' => $email],
            [
                'otp_hash' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0,
                'last_sent_at' => Carbon::now(),
            ]
        );

        $this->sendOtpEmail(
            user: $user,
            otp: $otp,
            subject: 'EduWrap Password Reset OTP',
            title: 'Reset your password',
            subtitle: 'Use this one-time code to continue your password reset.',
            accent: '#ea580c'
        );

        return ['message' => 'OTP sent to your registered email.'];
    }

    public function verifyForgotPasswordOtp(array $validated): array
    {
        $user = User::query()->where('email', $validated['email'])->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email.'],
            ]);
        }

        $otpRecord = PasswordResetOtp::query()->where('email', $validated['email'])->first();
        if (!$otpRecord || $otpRecord->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => ['OTP expired. Please request a new code.'],
            ]);
        }

        if (!Hash::check($validated['otp'], $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');
            if ($otpRecord->attempts >= 5) {
                $otpRecord->delete();
                throw ValidationException::withMessages([
                    'otp' => ['Too many invalid attempts. Request a new OTP.'],
                ]);
            }

            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP code.'],
            ]);
        }

        $resetToken = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => Hash::make($resetToken),
                'created_at' => Carbon::now(),
            ]
        );

        return [
            'message' => 'OTP verified. You can now reset your password.',
            'reset_token' => $resetToken,
        ];
    }

    public function resetPasswordWithOtp(array $validated): array
    {
        $tokenRow = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();
        if (!$tokenRow || !Hash::check($validated['reset_token'], $tokenRow->token)) {
            throw ValidationException::withMessages([
                'reset_token' => ['Invalid or expired reset token. Please verify OTP again.'],
            ]);
        }

        $createdAt = Carbon::parse($tokenRow->created_at);
        if ($createdAt->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            throw ValidationException::withMessages([
                'reset_token' => ['Reset session expired. Please verify OTP again.'],
            ]);
        }

        $user = User::query()->where('email', $validated['email'])->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        PasswordResetOtp::query()->where('email', $validated['email'])->delete();
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return [
            'message' => 'Password reset successfully.',
            'user' => $user,
            'token' => $token,
        ];
    }

    private function sendEmailVerificationOtp(User $user): void
    {
        $existing = EmailVerificationOtp::query()->where('user_id', $user->id)->first();
        if ($existing?->last_sent_at && $existing->last_sent_at->greaterThan(Carbon::now()->subSeconds(60))) {
            throw ValidationException::withMessages([
                'email' => ['Please wait a minute before requesting another OTP.'],
            ]);
        }

        $otp = (string) random_int(100000, 999999);
        EmailVerificationOtp::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp_hash' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0,
                'last_sent_at' => Carbon::now(),
            ]
        );

        $this->sendOtpEmail(
            user: $user,
            otp: $otp,
            subject: 'EduWrap Email Verification OTP',
            title: 'Verify your email',
            subtitle: 'Welcome to EduWrap! Enter this code to verify your account.',
            accent: '#9333ea'
        );
    }

    private function sendOtpEmail(
        User $user,
        string $otp,
        string $subject,
        string $title,
        string $subtitle,
        string $accent
    ): void {
        Mail::send(
            'emails.otp',
            [
                'name' => $user->name,
                'otp' => $otp,
                'title' => $title,
                'subtitle' => $subtitle,
                'accent' => $accent,
                'expiresInMinutes' => 10,
            ],
            static function ($message) use ($user, $subject) {
                $message->to($user->email)->subject($subject);
            }
        );
    }

    private function sendEmailVerificationOtpSafely(User $user, string $context): bool
    {
        try {
            $this->sendEmailVerificationOtp($user);
            return true;
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('auth.verification_otp_send_failed', [
                'context' => $context,
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
            return false;
        }
    }
}
