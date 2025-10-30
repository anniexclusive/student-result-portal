<?php

declare(strict_types=1);

use App\Http\Requests\CheckResultRequest;
use Illuminate\Support\Facades\Validator;

test('validates required fields correctly', function (array $data, bool $shouldPass, ?string $expectedError) {
    $request = new CheckResultRequest;
    $validator = Validator::make($data, $request->rules());

    if ($shouldPass) {
        expect($validator->passes())->toBeTrue();
    } else {
        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has($expectedError))->toBeTrue();
    }
})->with([
    'all fields present' => [
        ['pin' => '12345678', 'serial_number' => 'SN12345678', 'reg_number' => 'EX123456789'],
        true,
        null,
    ],
    'missing pin' => [
        ['serial_number' => 'SN12345678', 'reg_number' => 'EX123456789'],
        false,
        'pin',
    ],
    'missing serial number' => [
        ['pin' => '12345678', 'reg_number' => 'EX123456789'],
        false,
        'serial_number',
    ],
    'missing reg number' => [
        ['pin' => '12345678', 'serial_number' => 'SN12345678'],
        false,
        'reg_number',
    ],
    'empty pin' => [
        ['pin' => '', 'serial_number' => 'SN12345678', 'reg_number' => 'EX123456789'],
        false,
        'pin',
    ],
]);
