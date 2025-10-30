<?php

declare(strict_types=1);

use App\Models\Result;

test('hasPassed returns correct value based on remark', function (string $remark, bool $expected) {
    $result = Result::factory()->create(['remark' => $remark]);

    expect($result->hasPassed())->toBe($expected);
})->with([
    'PASS uppercase' => ['PASS', true],
    'PASSED uppercase' => ['PASSED', true],
    'SUCCESS uppercase' => ['SUCCESS', true],
    'pass lowercase' => ['pass', true],
    'PaSs mixed case' => ['PaSs', true],
    'FAIL uppercase' => ['FAIL', false],
    'empty remark' => ['', false],
    'null remark' => ['null', false],
    'random text' => ['random', false],
]);
