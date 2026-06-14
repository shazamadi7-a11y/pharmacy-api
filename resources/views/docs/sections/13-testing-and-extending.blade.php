<h2>11. Testing &amp; Extending</h2>

<h3>Test Suite Overview</h3>

<p>The project uses <strong>Pest PHP</strong> with the Laravel plugin. Tests run against an in-memory SQLite database (<span class="inline-code">:memory:</span>), so no external database is needed.</p>

<table>
    <thead>
        <tr><th>Test File</th><th>Tests</th><th>Coverage</th></tr>
    </thead>
    <tbody>
        <tr><td>AuthTest</td><td>13</td><td>Register (with phone), login, logout, me endpoint</td></tr>
        <tr><td>EmailVerificationTest</td><td>9</td><td>Verify, resend, rate limiting</td></tr>
        <tr><td>PasswordResetTest</td><td>7</td><td>Forgot password, reset, invalid token</td></tr>
        <tr><td>CategoryTest</td><td>16</td><td>CRUD, admin middleware, slug generation</td></tr>
        <tr><td>MedicineTest</td><td>17</td><td>CRUD, filtering, sorting, pagination, toggle, manufacturer</td></tr>
        <tr><td>CartTest</td><td>12</td><td>Add/update/remove/clear, stock validation, ownership</td></tr>
        <tr><td>OrderTest</td><td>18</td><td>Place order, stock deduction, cart clearing, price snapshot, admin lifecycle</td></tr>
        <tr><td>OtpTest</td><td>8</td><td>Send OTP, verify OTP, expiry, validation</td></tr>
        <tr><td>NotificationTest</td><td>8</td><td>Order status notifications, list/unread/read, low stock</td></tr>
        <tr><td>ReportTest</td><td>7</td><td>Sales report, inventory report, frequently out of stock</td></tr>
        <tr><td>ReceiptTest</td><td>4</td><td>Customer receipt, admin receipt, auth guards</td></tr>
        <tr><td><strong>Total</strong></td><td><strong>122</strong></td><td><strong>244 assertions</strong></td></tr>
    </tbody>
</table>

<h3>Running Tests</h3>

<div class="code-block"># Run all tests
./vendor/bin/pest

# Run a specific test file
./vendor/bin/pest tests/Feature/Api/V1/CategoryTest.php

# Run a specific test by name
./vendor/bin/pest --filter="it admin creates a category"

# Run full suite (lint + types + tests)
composer test

# Lint check only
composer test:lint

# Static analysis only
composer test:types</div>

<h3>Test Structure Pattern</h3>

<div class="code-block">&lt;?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
});

describe('Feature Group', function (): void {
    it('does something specific', function (): void {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/endpoint', ['key' => 'value']);

        $response->assertStatus(201)
            ->assertJsonPath('data.key', 'value');
    });
});</div>

<h3>Factory States</h3>

<div class="code-block">User::factory()->create();              // Customer (default)
User::factory()->admin()->create();      // Admin user
User::factory()->unverified()->create(); // Unverified email
Medicine::factory()->unavailable()->create();  // Unavailable medicine
Medicine::factory()->outOfStock()->create();   // Zero stock</div>

<h3>How to Add a New Endpoint</h3>

<ol>
    <li><strong>Create a Form Request</strong> in <span class="inline-code">app/Http/Requests/Api/V1/</span> with validation rules</li>
    <li><strong>Add controller method</strong> in the appropriate controller (extend <span class="inline-code">ApiController</span>)</li>
    <li><strong>Register the route</strong> in <span class="inline-code">routes/api/v1.php</span> with appropriate middleware</li>
    <li><strong>Write tests</strong> in <span class="inline-code">tests/Feature/Api/V1/</span></li>
    <li><strong>Run the full suite:</strong> <span class="inline-code">composer test</span></li>
</ol>

<h3>How to Add a New Model</h3>

<ol>
    <li><strong>Create migration:</strong> <span class="inline-code">php artisan make:migration create_tablename_table</span></li>
    <li><strong>Create model:</strong> Add to <span class="inline-code">app/Models/</span> with <span class="inline-code">declare(strict_types=1)</span> and <span class="inline-code">final class</span></li>
    <li><strong>Create factory:</strong> Add to <span class="inline-code">database/factories/</span></li>
    <li><strong>Define relationships</strong> in the model and related models</li>
    <li><strong>Create controller, requests, and routes</strong> following existing patterns</li>
</ol>

<h3>Code Style Rules</h3>

<table>
    <thead>
        <tr><th>Rule</th><th>Enforced By</th></tr>
    </thead>
    <tbody>
        <tr><td><span class="inline-code">declare(strict_types=1)</span> in all PHP files</td><td>Pint / Rector</td></tr>
        <tr><td><span class="inline-code">final</span> on all concrete classes</td><td>Pint</td></tr>
        <tr><td>Strict comparisons (<span class="inline-code">===</span>)</td><td>Rector</td></tr>
        <tr><td>PHPStan max level, no ignored errors</td><td>PHPStan</td></tr>
        <tr><td>Ordered class elements: traits, constants, properties, constructor, methods</td><td>Pint</td></tr>
    </tbody>
</table>
