<h2>2. Setup Guide</h2>

<h3>2.1 Prerequisites</h3>

<ul>
    <li>PHP 8.2 or higher</li>
    <li>Composer 2.x</li>
    <li>SQLite extension enabled (default for development)</li>
    <li>Git</li>
</ul>

<h3>2.2 Installation Steps</h3>

<div class="code-block"># 1. Clone the repository
git clone &lt;repository-url&gt;
cd pharmacy-api

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Create SQLite database
touch database/database.sqlite

# 6. Run migrations
php artisan migrate

# 7. Seed the database (creates admin, test user, categories, medicines, orders)
php artisan db:seed

# 8. Start the development server
php artisan serve</div>

<div class="info-box">
    <strong>Auto-generated API docs:</strong> Once the server is running, visit
    <span class="inline-code">http://localhost:8000/docs/api</span> for Swagger UI or
    <span class="inline-code">http://localhost:8000/docs/api.json</span> for raw OpenAPI JSON.
</div>

<h3>2.3 Seeded Users</h3>

<table>
    <thead>
        <tr><th>User</th><th>Email</th><th>Password</th><th>Role</th></tr>
    </thead>
    <tbody>
        <tr><td>Admin User</td><td><span class="inline-code">admin@pharmacy.com</span></td><td><span class="inline-code">password</span></td><td>admin</td></tr>
        <tr><td>Test User</td><td><span class="inline-code">test@example.com</span></td><td><span class="inline-code">password</span></td><td>customer</td></tr>
    </tbody>
</table>

<h3>2.4 Seeded Data</h3>

<table>
    <thead>
        <tr><th>Table</th><th>Count</th><th>Details</th></tr>
    </thead>
    <tbody>
        <tr><td>Categories</td><td>6</td><td>Antibiotics, Painkillers, Vitamins, Supplements, Skincare, First Aid</td></tr>
        <tr><td>Medicines</td><td>17</td><td>Realistic brands across all categories, with 1-2 images each</td></tr>
        <tr><td>Orders</td><td>6</td><td>For test customer &mdash; mix of pending, confirmed, completed, rejected</td></tr>
        <tr><td>Order Items</td><td>13</td><td>1-3 items per order with realistic quantities and prices</td></tr>
    </tbody>
</table>

<h3>2.5 Useful Commands</h3>

<table>
    <thead>
        <tr><th>Command</th><th>Description</th></tr>
    </thead>
    <tbody>
        <tr><td><span class="inline-code">php artisan serve</span></td><td>Start development server</td></tr>
        <tr><td><span class="inline-code">./vendor/bin/pest</span></td><td>Run all tests</td></tr>
        <tr><td><span class="inline-code">composer lint</span></td><td>Lint &amp; fix (Pint + Rector)</td></tr>
        <tr><td><span class="inline-code">composer test:types</span></td><td>Static analysis (PHPStan max)</td></tr>
        <tr><td><span class="inline-code">composer test</span></td><td>Run everything (lint + types + tests)</td></tr>
        <tr><td><span class="inline-code">php artisan migrate:fresh --seed</span></td><td>Reset database with fresh data</td></tr>
    </tbody>
</table>

<div class="page-break"></div>
