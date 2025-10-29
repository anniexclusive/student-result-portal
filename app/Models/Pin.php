<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pin extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pin',
        'serial_number',
        'count',
        'result_id',
        'use_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'count' => 'integer',
        ];
    }

    /**
     * Get the result associated with this PIN.
     */
    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    /**
     * Check if the PIN has been used.
     */
    public function isUsed(): bool
    {
        return $this->use_status === 'used';
    }

    /**
     * Check if the PIN has expired (exceeded max usage count).
     */
    public function hasExpired(): bool
    {
        return $this->count >= 5;
    }

    /**
     * Check if the PIN can be used for a specific result.
     */
    public function canBeUsedFor(?Result $result): bool
    {
        if ($this->hasExpired()) {
            return false;
        }

        // If PIN is unused or used by the same result
        return ! $this->isUsed() || $this->result_id === $result?->id;
    }
}
