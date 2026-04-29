<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'action' => ['required', 'in:add,remove'],
        ]);

        if ($validated['action'] === 'add') {
            $cart = $this->cartService->addCourse(
                $request->user(),
                Course::findOrFail($validated['course_id'])
            );
        } else {
            $cart = $this->cartService->removeCourse($request->user(), (int) $validated['course_id']);
        }

        return response()->json($cart);
    }

    public function show(Request $request)
    {
        return response()->json($this->cartService->getCart($request->user()));
    }
}
