<?php

namespace App\Services;

use App\Repositories\ActivityRepository;

class AnalyticsService
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
    ) {
    }

    public function getInsights(): array
    {
        return $this->activityRepository->insights();
    }
}
