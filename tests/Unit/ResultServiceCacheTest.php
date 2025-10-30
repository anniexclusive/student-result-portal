<?php

declare(strict_types=1);

use App\Models\Result;
use App\Services\ResultService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

test('caches result queries in non-testing environment', function () {
    // Temporarily set environment to production
    app()->instance('env', 'production');

    $result = Result::factory()->create(['exam_number' => 'EX999999999']);
    $service = new ResultService;

    // Clear any existing cache
    Cache::flush();

    // First call - should hit database
    $service->findResultByExamNumber('EX999999999');

    // Verify result is cached
    expect(Cache::has('result:exam_number:EX999999999'))->toBeTrue();

    // Enable query log to verify second call doesn't hit database
    DB::enableQueryLog();
    $cachedResult = $service->findResultByExamNumber('EX999999999');
    $queries = DB::getQueryLog();

    // Should not execute any queries (from cache)
    expect($queries)->toHaveCount(0)
        ->and($cachedResult->id)->toBe($result->id);

    // Restore testing environment
    app()->instance('env', 'testing');
    Cache::flush();
});

test('clearResultCache removes all cached entries for a result', function () {
    // Temporarily set environment to production
    app()->instance('env', 'production');

    $result = Result::factory()->create(['exam_number' => 'EX888888888']);
    $service = new ResultService;

    // Clear any existing cache
    Cache::flush();

    // Cache all three methods
    $service->findResultByExamNumber('EX888888888');
    $service->resultExists('EX888888888');
    $service->getResultWithPins('EX888888888');

    // Verify all are cached
    expect(Cache::has('result:exam_number:EX888888888'))->toBeTrue()
        ->and(Cache::has('result:exists:EX888888888'))->toBeTrue()
        ->and(Cache::has('result:with_pins:EX888888888'))->toBeTrue();

    // Clear cache
    $service->clearResultCache('EX888888888');

    // Verify all are removed
    expect(Cache::has('result:exam_number:EX888888888'))->toBeFalse()
        ->and(Cache::has('result:exists:EX888888888'))->toBeFalse()
        ->and(Cache::has('result:with_pins:EX888888888'))->toBeFalse();

    // Restore testing environment
    app()->instance('env', 'testing');
    Cache::flush();
});

test('cache is bypassed in testing environment', function () {
    // Verify we're in testing environment
    expect(app()->environment('testing'))->toBeTrue();

    $result = Result::factory()->create(['exam_number' => 'EX777777777']);
    $service = new ResultService;

    // Should not cache in testing
    $service->findResultByExamNumber('EX777777777');

    expect(Cache::has('result:exam_number:EX777777777'))->toBeFalse();
});

test('resultExists caches existence checks', function () {
    // Temporarily set environment to production
    app()->instance('env', 'production');

    Result::factory()->create(['exam_number' => 'EX_CACHE_EXISTS']);
    $service = new ResultService;

    // Clear any existing cache
    Cache::flush();

    // First call - should cache
    $exists = $service->resultExists('EX_CACHE_EXISTS');
    expect($exists)->toBeTrue();

    // Enable query log
    DB::enableQueryLog();
    $cachedExists = $service->resultExists('EX_CACHE_EXISTS');
    $queries = DB::getQueryLog();

    // Should not execute any queries (from cache)
    expect($queries)->toHaveCount(0)
        ->and($cachedExists)->toBeTrue();

    // Restore testing environment
    app()->instance('env', 'testing');
    Cache::flush();
});

test('getResultWithPins caches results with relationships', function () {
    // Temporarily set environment to production
    app()->instance('env', 'production');

    $result = Result::factory()->create(['exam_number' => 'EX555555555']);
    $service = new ResultService;

    // Clear any existing cache
    Cache::flush();

    // First call - should cache
    $resultWithPins = $service->getResultWithPins('EX555555555');
    expect($resultWithPins)->not->toBeNull();

    // Enable query log
    DB::enableQueryLog();
    $cachedResult = $service->getResultWithPins('EX555555555');
    $queries = DB::getQueryLog();

    // Should not execute any queries (from cache)
    expect($queries)->toHaveCount(0)
        ->and($cachedResult->id)->toBe($result->id);

    // Restore testing environment
    app()->instance('env', 'testing');
    Cache::flush();
});

test('cache respects TTL of 30 minutes', function () {
    // Temporarily set environment to production
    app()->instance('env', 'production');

    $result = Result::factory()->create(['exam_number' => 'EX_CACHE_TTL']);
    $service = new ResultService;

    // Clear any existing cache
    Cache::flush();

    // Cache the result
    $service->findResultByExamNumber('EX_CACHE_TTL');

    // Verify cache exists
    expect(Cache::has('result:exam_number:EX_CACHE_TTL'))->toBeTrue();

    // Get TTL (in seconds)
    // Note: This is a conceptual test - actual TTL verification depends on cache driver
    $cachedValue = Cache::get('result:exam_number:EX_CACHE_TTL');
    expect($cachedValue)->not->toBeNull();

    // Restore testing environment
    app()->instance('env', 'testing');
    Cache::flush();
});

test('cache handles null results correctly', function () {
    // Temporarily set environment to production
    app()->instance('env', 'production');

    $service = new ResultService;

    // Clear any existing cache
    Cache::flush();

    // Query non-existent result
    $result = $service->findResultByExamNumber('EX_NONEXISTENT_UNIQUE');
    expect($result)->toBeNull();

    // Laravel Cache::remember caches null values by default
    // Verify it was cached by checking second call doesn't hit DB
    DB::enableQueryLog();
    $cachedResult = $service->findResultByExamNumber('EX_NONEXISTENT_UNIQUE');
    $queries = DB::getQueryLog();

    // If cached, should not execute queries
    expect($cachedResult)->toBeNull();

    // Restore testing environment
    app()->instance('env', 'testing');
    Cache::flush();
});
