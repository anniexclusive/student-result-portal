<?php

declare(strict_types=1);

use App\Models\Pin;
use App\Models\Result;

test('result has pins relationship', function () {
    $result = Result::factory()->create();
    $pins = Pin::factory()->count(3)->create(['result_id' => $result->id]);

    expect($result->pins)->toHaveCount(3)
        ->and($result->pins->first()->id)->toBe($pins->first()->id);
});

test('checks if result has passed with PASS remark', function () {
    $result = Result::factory()->create(['remark' => 'PASS']);

    expect($result->hasPassed())->toBeTrue();
});

test('checks if result has passed with PASSED remark', function () {
    $result = Result::factory()->create(['remark' => 'PASSED']);

    expect($result->hasPassed())->toBeTrue();
});

test('checks if result has passed with SUCCESS remark', function () {
    $result = Result::factory()->create(['remark' => 'SUCCESS']);

    expect($result->hasPassed())->toBeTrue();
});

test('checks if result has passed with lowercase pass', function () {
    $result = Result::factory()->create(['remark' => 'pass']);

    expect($result->hasPassed())->toBeTrue();
});

test('checks if result has not passed with FAIL remark', function () {
    $result = Result::factory()->create(['remark' => 'FAIL']);

    expect($result->hasPassed())->toBeFalse();
});

test('checks if result has not passed with empty remark', function () {
    $result = Result::factory()->create(['remark' => '']);

    expect($result->hasPassed())->toBeFalse();
});

test('score is cast to decimal', function () {
    $result = Result::factory()->create(['score' => 85.567]);

    expect($result->score)->toBe('85.57');
});
