<?php

declare(strict_types=1);

use App\Http\Requests\CheckResultRequest;
use Illuminate\Support\Facades\Validator;

test('validation passes with all required fields', function () {
    $request = new CheckResultRequest();
    $validator = Validator::make([
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
        'reg_number' => 'EX123456789',
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

test('validation fails without pin', function () {
    $request = new CheckResultRequest();
    $validator = Validator::make([
        'serial_number' => 'SN12345678',
        'reg_number' => 'EX123456789',
    ], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('pin'))->toBeTrue();
});

test('validation fails without serial number', function () {
    $request = new CheckResultRequest();
    $validator = Validator::make([
        'pin' => '12345678',
        'reg_number' => 'EX123456789',
    ], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('serial_number'))->toBeTrue();
});

test('validation fails without reg number', function () {
    $request = new CheckResultRequest();
    $validator = Validator::make([
        'pin' => '12345678',
        'serial_number' => 'SN12345678',
    ], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('reg_number'))->toBeTrue();
});

test('validation fails with empty pin', function () {
    $request = new CheckResultRequest();
    $validator = Validator::make([
        'pin' => '',
        'serial_number' => 'SN12345678',
        'reg_number' => 'EX123456789',
    ], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('pin'))->toBeTrue();
});
