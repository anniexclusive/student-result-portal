<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Result;
use Illuminate\Support\Facades\Cache;

class ResultService
{
    /**
     * Cache TTL in seconds (30 minutes).
     */
    private const CACHE_TTL = 1800;

    /**
     * Find a result by examination number with caching.
     */
    public function findResultByExamNumber(string $examNumber): ?Result
    {
        // Skip caching in testing environment to avoid stale data issues
        if (app()->environment('testing')) {
            return Result::where('exam_number', $examNumber)->first();
        }

        $cacheKey = "result:exam_number:{$examNumber}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($examNumber) {
            return Result::where('exam_number', $examNumber)->first();
        });
    }

    /**
     * Check if a result exists.
     */
    public function resultExists(string $examNumber): bool
    {
        if (app()->environment('testing')) {
            return Result::where('exam_number', $examNumber)->exists();
        }

        $cacheKey = "result:exists:{$examNumber}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($examNumber) {
            return Result::where('exam_number', $examNumber)->exists();
        });
    }

    /**
     * Get result details with related PIN usage history.
     */
    public function getResultWithPins(string $examNumber): ?Result
    {
        if (app()->environment('testing')) {
            return Result::with('pins')
                ->where('exam_number', $examNumber)
                ->first();
        }

        $cacheKey = "result:with_pins:{$examNumber}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($examNumber) {
            return Result::with('pins')
                ->where('exam_number', $examNumber)
                ->first();
        });
    }

    /**
     * Clear cache for a specific result.
     */
    public function clearResultCache(string $examNumber): void
    {
        Cache::forget("result:exam_number:{$examNumber}");
        Cache::forget("result:exists:{$examNumber}");
        Cache::forget("result:with_pins:{$examNumber}");
    }
}
