<h2>1. Project Overview</h2>

<p>The Pharmacy API is a Laravel 12 API-only backend that powers a pharmacy e-commerce platform. It provides a versioned RESTful API consumed by any frontend (web, mobile, or desktop). There are no Blade views or frontend assets &mdash; this is purely a JSON API.</p>

<h3>1.1 Technology Stack</h3>

<table>
    <thead>
        <tr>
            <th style="width: 30%;">Technology</th>
            <th>Purpose</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Laravel 12</td><td>PHP framework (API-only, no frontend)</td></tr>
        <tr><td>Laravel Sanctum</td><td>Token-based authentication (Bearer tokens)</td></tr>
        <tr><td>Spatie Query Builder</td><td>Filtering, sorting, and pagination for list endpoints</td></tr>
        <tr><td>Cloudinary</td><td>Cloud image storage for categories and medicines</td></tr>
        <tr><td>Scramble</td><td>Auto-generated OpenAPI/Swagger documentation</td></tr>
        <tr><td>DomPDF</td><td>PDF receipt generation for orders</td></tr>
        <tr><td>Pest PHP</td><td>Testing framework (122 tests, 244 assertions)</td></tr>
        <tr><td>PHPStan (max level)</td><td>Static analysis</td></tr>
        <tr><td>Laravel Pint + Rector</td><td>Code formatting and refactoring</td></tr>
        <tr><td>SQLite (dev) / MySQL (prod)</td><td>Database</td></tr>
        <tr><td>grazulex/laravel-apiroute</td><td>URI-based API versioning (/api/v1/, /api/v2/)</td></tr>
    </tbody>
</table>

<h3>1.2 Architecture Diagram</h3>

<div class="erd-box">
routes/api/v1.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;v<br>
app/Http/Controllers/Api/V1/{Resource}Controller.php<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;extends ApiController (uses ApiResponse trait)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- app/Http/Requests/Api/V1/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<-- Form validation (one per action)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- app/Http/Resources/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<-- JSON transformers<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- app/Models/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<-- Eloquent models<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- database/migrations/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<-- Schema definitions
</div>

<h3>1.3 Response Format</h3>

<p>Every API response follows a standardized structure provided by the <span class="inline-code">ApiResponse</span> trait:</p>

<p><strong>Success Response:</strong></p>
<div class="code-block">{
  "success": true,
  "message": "Description of result",
  "data": { ... }
}</div>

<p><strong>Error Response:</strong></p>
<div class="code-block">{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}</div>

<h3>1.4 Rate Limiting</h3>

<table>
    <thead>
        <tr><th>Limiter</th><th>Rate</th><th>Applied To</th></tr>
    </thead>
    <tbody>
        <tr><td><span class="inline-code">auth</span></td><td>5 req/min</td><td>POST /login, /register</td></tr>
        <tr><td><span class="inline-code">authenticated</span></td><td>120 req/min</td><td>All protected routes</td></tr>
        <tr><td><span class="inline-code">api</span></td><td>60 req/min</td><td>Default for public routes</td></tr>
    </tbody>
</table>

<div class="page-break"></div>
