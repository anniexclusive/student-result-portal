<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pin;
use App\Models\Result;

class PinService
{
    /**
     * Find a PIN by PIN code and serial number.
     */
    public function findPin(string $pin, string $serialNumber): ?Pin
    {
        return Pin::where('pin', $pin)
            ->where('serial_number', $serialNumber)
            ->first();
    }

    /**
     * Validate if a PIN can be used for accessing a result.
     */
    public function validatePinUsage(Pin $pin, Result $result): array
    {
        if ($pin->hasExpired()) {
            return [
                'valid' => false,
                'message' => 'PIN has expired!!',
            ];
        }

        if (! $pin->canBeUsedFor($result)) {
            return [
                'valid' => false,
                'message' => 'PIN used by another user!',
            ];
        }

        return [
            'valid' => true,
            'message' => 'PIN is valid',
        ];
    }

    /**
     * Mark a PIN as used for a specific result.
     */
    public function markPinAsUsed(Pin $pin, Result $result): Pin
    {
        $pin->update([
            'count' => $pin->count + 1,
            'result_id' => $result->id,
            'use_status' => 'used',
        ]);

        return $pin->fresh();
    }

    /**
     * Validate and use a PIN for result access.
     */
    public function validateAndUsePinForResult(string $pin, string $serialNumber, Result $result): array
    {
        $pinModel = $this->findPin($pin, $serialNumber);

        if (! $pinModel) {
            return [
                'success' => false,
                'message' => 'Invalid PIN and Serial Number',
            ];
        }

        $validation = $this->validatePinUsage($pinModel, $result);

        if (! $validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
            ];
        }

        $this->markPinAsUsed($pinModel, $result);

        return [
            'success' => true,
            'pin' => $pinModel,
        ];
    }
}
