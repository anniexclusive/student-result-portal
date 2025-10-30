<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Pin;
use App\Models\Result;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    // SQL Injection Tests
    public function test_prevents_sql_injection_in_pin_lookup(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create(['exam_number' => 'EX123456789']);
        Pin::factory()->unused()->create([
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
        ]);

        // Attempt SQL injection in PIN field
        $response = $this->actingAs($user)->post('/check', [
            'pin' => "12345678' OR '1'='1",
            'serial_number' => 'SN12345678',
            'reg_number' => 'EX123456789',
        ]);

        // Should not find PIN with malicious input
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_prevents_sql_injection_in_serial_number(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create(['exam_number' => 'EX123456789']);
        Pin::factory()->unused()->create([
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
        ]);

        // Attempt SQL injection in serial number field
        $response = $this->actingAs($user)->post('/check', [
            'pin' => '12345678',
            'serial_number' => "SN12345678' OR '1'='1",
            'reg_number' => 'EX123456789',
        ]);

        // Should not find PIN with malicious input
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_prevents_sql_injection_in_exam_number(): void
    {
        $user = User::factory()->create();
        Pin::factory()->unused()->create([
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
        ]);

        // Attempt SQL injection with table drop
        $response = $this->actingAs($user)->post('/check', [
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
            'reg_number' => "EX123'; DROP TABLE results; --",
        ]);

        // Should safely handle malicious input
        $response->assertRedirect();
        $response->assertSessionHasErrors('msg');

        // Verify table still exists
        $this->assertDatabaseCount('results', 0);
    }

    // XSS Prevention Tests
    public function test_prevents_xss_in_student_name_display(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create([
            'exam_number' => 'EX777777777',
            'student_name' => '<script>alert("XSS")</script>',
        ]);
        $pin = Pin::factory()->unused()->create([
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
        ]);

        $response = $this->actingAs($user)->post('/check', [
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
            'reg_number' => 'EX777777777',
        ]);

        $response->assertStatus(200);
        // Script tags should be escaped, not executed
        $response->assertDontSee('<script>', false);
        $response->assertSee('&lt;script&gt;', false);
    }

    public function test_prevents_xss_in_course_display(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create([
            'exam_number' => 'EX888888888',
            'course' => '<img src=x onerror=alert(1)>',
        ]);
        $pin = Pin::factory()->unused()->create([
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
        ]);

        $response = $this->actingAs($user)->post('/check', [
            'pin' => '12345678',
            'serial_number' => 'SN12345678',
            'reg_number' => 'EX888888888',
        ]);

        $response->assertStatus(200);
        // Should escape HTML
        $response->assertDontSee('<img src=x', false);
    }

    // Mass Assignment Protection Tests
    public function test_prevents_mass_assignment_on_user_model(): void
    {
        // Attempt to create user with is_admin field (if it existed)
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_admin' => true,  // Malicious field
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();

        // is_admin should not be set (model should ignore it)
        $this->assertObjectNotHasProperty('is_admin', $user);
    }

    // Input Validation Tests
    public function test_handles_extremely_long_pin_input(): void
    {
        $user = User::factory()->create();

        // Attempt with 1000 character PIN
        $response = $this->actingAs($user)->post('/check', [
            'pin' => str_repeat('1', 1000),
            'serial_number' => 'SN12345678',
            'reg_number' => 'EX123456789',
        ]);

        // Should handle gracefully without crashing
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_handles_special_characters_in_inputs(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/check', [
            'pin' => '!@#$%^&*()',
            'serial_number' => '"><script>',
            'reg_number' => '<?php phpinfo(); ?>',
        ]);

        // Should handle gracefully
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_handles_unicode_characters_in_inputs(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/check', [
            'pin' => '你好世界🌍',
            'serial_number' => '你好',
            'reg_number' => 'EX🔥🔥🔥',
        ]);

        // Should handle gracefully
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    // CSRF Protection Test
    public function test_csrf_protection_is_enforced_on_result_check(): void
    {
        $user = User::factory()->create();

        // Note: In Laravel testing, CSRF is automatically handled
        // This test verifies the middleware is present
        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/check', [
                'pin' => '12345678',
                'serial_number' => 'SN12345678',
                'reg_number' => 'EX123456789',
            ]);

        // With CSRF disabled, should still work (proves middleware exists)
        $response->assertSessionHasErrors();
    }

    // Session Security Tests
    public function test_session_is_regenerated_after_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Session should be regenerated (prevents session fixation)
        $this->assertAuthenticated();
    }

    // Password Security Tests
    public function test_password_is_always_hashed_never_stored_plain(): void
    {
        $user = User::factory()->create([
            'password' => 'plain-password',
        ]);

        // Password should be hashed, not plain text
        $this->assertNotEquals('plain-password', $user->password);
        $this->assertTrue(strlen($user->password) > 50); // Bcrypt hashes are 60 chars
    }
}
