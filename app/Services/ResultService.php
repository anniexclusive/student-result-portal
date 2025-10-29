<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Result;

class ResultService
{
    /**
     * Find a result by examination number.
     */
    public function findResultByExamNumber(string $examNumber): ?Result
    {
        return Result::where('exam_number', $examNumber)->first();
    }

    /**
     * Check if a result exists.
     */
    public function resultExists(string $examNumber): bool
    {
        return Result::where('exam_number', $examNumber)->exists();
    }

    /**
     * Get result details with related PIN usage history.
     */
    public function getResultWithPins(string $examNumber): ?Result
    {
        return Result::with('pins')
            ->where('exam_number', $examNumber)
            ->first();
    }
}
