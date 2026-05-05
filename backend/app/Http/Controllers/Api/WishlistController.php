<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(
        private readonly WishlistService $wishlistService,
    ) {
    }

    public function show(Request $request)
    {
        return response()->json($this->wishlistService->getWishlist($request->user()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'action' => ['required', 'in:add,remove'],
        ]);

        if ($validated['action'] === 'add') {
            $wishlist = $this->wishlistService->addCourse(
                $request->user(),
                Course::findOrFail($validated['course_id'])
            );
        } else {
            $wishlist = $this->wishlistService->removeCourse($request->user(), (int) $validated['course_id']);
        }

        return response()->json($wishlist);
    }
}
