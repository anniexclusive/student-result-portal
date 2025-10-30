<?php

declare(strict_types=1);

use App\Models\Pin;
use App\Models\Result;
use App\Services\PinService;
use App\Services\ResultService;

// Null/Empty Handling Tests
test('PIN handles null result reference gracefully', function () {
    $pin = Pin::factory()->unused()->create();

    // Accessing result when none is set should not crash
    $result = $pin->result;

    expect($result)->toBeNull();
});

test('ResultService handles empty exam number', function () {
    $service = new ResultService;

    $result = $service->findResultByExamNumber('');

    expect($result)->toBeNull();
});

test('ResultService handles whitespace-only exam number', function () {
    $service = new ResultService;

    $result = $service->findResultByExamNumber('   ');

    expect($result)->toBeNull();
});

test('PinService handles non-existent PIN gracefully', function () {
    $service = new PinService;

    $pin = $service->findPin('NONEXISTENT', 'NONEXISTENT');

    expect($pin)->toBeNull();
});

// Orphaned Record Handling
test('handles PIN with invalid result_id reference', function () {
    $result = Result::factory()->create(['exam_number' => 'EX_TEMP_ORPHAN']);
    $pin = Pin::factory()->create([
        'result_id' => $result->id,
        'use_status' => 'used',
    ]);

    // Delete the result, making PIN orphaned
    $result->delete();

    // Refresh PIN and check relationship
    $pin->refresh();
    expect($pin->result)->toBeNull();
});

test('validates PIN can handle result deletion', function () {
    $result = Result::factory()->create(['exam_number' => 'EX123456789']);
    $pin = Pin::factory()->used($result)->create();

    // Delete the result
    $result->delete();

    // PIN should handle deleted result
    $pin->refresh();
    expect($pin->result)->toBeNull();
});

// Database Constraint Tests
test('enforces unique exam numbers', function () {
    Result::factory()->create(['exam_number' => 'EX_UNIQUE_TEST']);

    // Attempting to create duplicate should fail
    try {
        Result::factory()->create(['exam_number' => 'EX_UNIQUE_TEST']);
        $this->fail('Expected unique constraint violation');
    } catch (\Illuminate\Database\QueryException $e) {
        expect($e->getCode())->toBe('23000'); // Integrity constraint violation
    }
});

test('handles very long student names', function () {
    // Use 255 chars (typical VARCHAR limit)
    $longName = str_repeat('A', 255);

    $result = Result::factory()->create([
        'student_name' => $longName,
        'exam_number' => 'EX_LONG_NAME',
    ]);

    expect(strlen($result->student_name))->toBeLessThanOrEqual(255);
});

// Edge Cases for Business Logic
test('PIN count never goes negative', function () {
    $result = Result::factory()->create(['exam_number' => 'EX123456789']);
    $pin = Pin::factory()->used($result)->create(['count' => 0]);

    // Attempt to use PIN (should handle count = 0)
    $canUse = $pin->canBeUsedFor($result);

    expect($canUse)->toBeTrue(); // Should allow usage
});

test('validates PIN usage at exact boundary', function () {
    $result = Result::factory()->create(['exam_number' => 'EX_BOUNDARY_EXACT']);
    $pin = Pin::factory()->used($result)->create(['count' => 4]);

    // At count=4, should still be usable
    expect($pin->canBeUsedFor($result))->toBeTrue();

    // At count=5, PIN is expired (hasExpired returns true when count >= 5)
    $pin->count = 5;
    expect($pin->canBeUsedFor($result))->toBeFalse();

    // At count=6, still expired
    $pin->count = 6;
    expect($pin->canBeUsedFor($result))->toBeFalse();
});

test('handles empty string remark as null equivalent', function () {
    // Since database doesn't allow actual NULL, test empty string
    $result = Result::factory()->create([
        'exam_number' => 'EX_EMPTY_REMARK_TEST',
        'remark' => '',
    ]);

    // Empty remark should be treated as not passed
    expect($result->hasPassed())->toBeFalse();
});

test('handles remark with only whitespace', function () {
    $result = Result::factory()->create(['remark' => '   ']);

    expect($result->hasPassed())->toBeFalse();
});

// Data Integrity Tests
test('PIN result_id matches actual relationship', function () {
    $result = Result::factory()->create(['exam_number' => 'EX_INTEGRITY_TEST']);
    $pin = Pin::factory()->used($result)->create();

    // Verify relationship integrity
    expect($pin->result_id)->toBe($result->id);

    // Verify relationship works
    $relatedResult = $pin->result;
    expect($relatedResult)->not->toBeNull()
        ->and($relatedResult->id)->toBe($result->id);
});

test('deleting result does not cascade delete PINs', function () {
    $result = Result::factory()->create(['exam_number' => 'EX_CASCADE_TEST']);
    $pin = Pin::factory()->used($result)->create();

    $pinId = $pin->id;
    $result->delete();

    // PIN should still exist
    $pin = Pin::find($pinId);
    expect($pin)->not->toBeNull();
});
