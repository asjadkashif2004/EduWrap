<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
    ) {
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'avatar_url' => ['sometimes', 'nullable', 'url'],
            'fcm_token' => ['sometimes', 'nullable', 'string', 'max:400'],
        ]);

        return response()->json($this->profileService->updateProfile($request->user(), $validated));
    }

    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate([
            // `image` can reject some camera/gallery files; `mimes` + size is enough for avatars
            'avatar' => ['required', 'file', 'mimes:jpg,jpeg,jpe,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $path = $validated['avatar']->store('avatars', 'public');
        $path = str_replace('\\', '/', $path);
        // Prefer the host the client used (LAN IP, ngrok, etc.) so stored URLs load on phones;
        // fall back to APP_URL for odd CLI / proxy setups.
        $fromRequest = rtrim($request->getSchemeAndHttpHost(), '/');
        $fromConfig = rtrim((string) config('app.url'), '/');
        $base = $fromRequest !== '' ? $fromRequest : $fromConfig;
        $avatarUrl = $base . '/storage/' . ltrim($path, '/');

        if (!empty($user->avatar_url) && preg_match('#/storage/(.+)$#u', $user->avatar_url, $matches)) {
            $previousPath = $matches[1];
            if ($previousPath !== $path && Storage::disk('public')->exists($previousPath)) {
                Storage::disk('public')->delete($previousPath);
            }
        }

        $updated = $this->profileService->updateProfile($user, [
            'avatar_url' => $avatarUrl,
        ]);

        return response()->json($updated);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $updated = $this->profileService->updatePassword(
            $request->user(),
            $validated['current_password'],
            $validated['password']
        );

        if (!$updated) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        return response()->json(['message' => 'Password updated']);
    }
}
