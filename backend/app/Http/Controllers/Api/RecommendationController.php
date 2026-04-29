<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly RecommendationService $recommendationService,
    ) {
    }

    public function index(Request $request)
    {
        return response()->json($this->recommendationService->recommendForUser($request->user()));
    }
}
