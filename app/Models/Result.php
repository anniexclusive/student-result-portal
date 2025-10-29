<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Result extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exam_number',
        'student_name',
        'course',
        'score',
        'grade',
        'remark',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    /**
     * Get all PINs used to access this result.
     */
    public function pins(): HasMany
    {
        return $this->hasMany(Pin::class);
    }

    /**
     * Check if the result passed.
     */
    public function hasPassed(): bool
    {
        return in_array(strtoupper($this->remark ?? ''), ['PASS', 'PASSED', 'SUCCESS']);
    }
}
