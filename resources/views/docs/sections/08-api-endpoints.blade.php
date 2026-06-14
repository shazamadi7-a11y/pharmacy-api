<h2>6. API Endpoints</h2>

<p>Base URL: <span class="inline-code">http://localhost:8000/api/v1</span> &mdash; All endpoints use JSON (<span class="inline-code">application/json</span>) unless noted otherwise.</p>

<div class="info-box">
    <strong>Legend:</strong>
    <span class="badge badge-get">GET</span>
    <span class="badge badge-post">POST</span>
    <span class="badge badge-put">PUT</span>
    <span class="badge badge-delete">DELETE</span>
    <span class="badge badge-patch">PATCH</span>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <span class="badge badge-auth">AUTH</span> = Requires Bearer token
    &nbsp;&nbsp;
    <span class="badge badge-public">PUBLIC</span> = No auth needed
    &nbsp;&nbsp;
    <span class="badge badge-admin">ADMIN</span> = Admin role only
</div>

@php
    $currentSection = '';
@endphp

@foreach($endpoints as $endpoint)
    @if($endpoint['section'] !== $currentSection)
        @php $currentSection = $endpoint['section']; @endphp
        <h3>{{ $currentSection }}</h3>
    @endif

    <div class="endpoint-card avoid-break">
        <div class="endpoint-header">
            @php
                $methodLower = strtolower($endpoint['method']);
                $badgeClass = match($methodLower) {
                    'get' => 'badge-get',
                    'post' => 'badge-post',
                    'put' => 'badge-put',
                    'delete' => 'badge-delete',
                    'patch' => 'badge-patch',
                    default => 'badge-get',
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $endpoint['method'] }}</span>
            <span class="endpoint-path">{{ $endpoint['path'] }}</span>
            &nbsp;&nbsp;
            @if($endpoint['auth'])
                <span class="badge badge-auth">AUTH</span>
            @else
                <span class="badge badge-public">PUBLIC</span>
            @endif
            @if($endpoint['role'] === 'admin')
                <span class="badge badge-admin">ADMIN</span>
            @endif
            <span class="status-badge">{{ $endpoint['status'] }}</span>
        </div>

        <p>{{ $endpoint['description'] }}</p>

        @if($endpoint['request'])
            <p><strong>Request Body:</strong></p>
            <div class="code-block">{{ $endpoint['request'] }}</div>
        @endif

        @if($endpoint['response'] && !str_contains($endpoint['response'], 'PDF file'))
            <p><strong>Response:</strong></p>
            <div class="code-block">{{ $endpoint['response'] }}</div>
        @elseif($endpoint['response'] && str_contains($endpoint['response'], 'PDF file'))
            <p><strong>Response:</strong> {{ $endpoint['response'] }}</p>
        @endif
    </div>
@endforeach

<h3>Medicine Filtering &amp; Sorting</h3>

<p>The <span class="inline-code">GET /api/v1/medicines</span> endpoint supports advanced filtering via query parameters:</p>

<table>
    <thead>
        <tr><th>Parameter</th><th>Example</th><th>Description</th></tr>
    </thead>
    <tbody>
        <tr><td><span class="inline-code">filter[search]</span></td><td>Panadol</td><td>Search in brand_name AND scientific_name</td></tr>
        <tr><td><span class="inline-code">filter[category_id]</span></td><td>2</td><td>Filter by category</td></tr>
        <tr><td><span class="inline-code">filter[is_available]</span></td><td>1 or 0</td><td>Filter by availability</td></tr>
        <tr><td><span class="inline-code">filter[requires_prescription]</span></td><td>1 or 0</td><td>Filter by prescription requirement</td></tr>
        <tr><td><span class="inline-code">sort</span></td><td>price, -price</td><td>Sort ascending or descending (prefix -)</td></tr>
        <tr><td><span class="inline-code">per_page</span></td><td>15</td><td>Items per page (default: 15)</td></tr>
        <tr><td><span class="inline-code">page</span></td><td>2</td><td>Page number</td></tr>
    </tbody>
</table>

<p><strong>Example:</strong></p>
<div class="code-block">GET /api/v1/medicines?filter[search]=amox&filter[is_available]=1&sort=price&per_page=10</div>

<h3>HTTP Status Codes</h3>

<table>
    <thead>
        <tr><th>Code</th><th>Meaning</th><th>When</th></tr>
    </thead>
    <tbody>
        <tr><td>200</td><td>OK</td><td>Successful GET, PUT, PATCH, DELETE</td></tr>
        <tr><td>201</td><td>Created</td><td>Successful POST (resource created)</td></tr>
        <tr><td>401</td><td>Unauthorized</td><td>Missing or invalid token</td></tr>
        <tr><td>403</td><td>Forbidden</td><td>Insufficient permissions (non-admin, wrong owner)</td></tr>
        <tr><td>404</td><td>Not Found</td><td>Resource doesn't exist</td></tr>
        <tr><td>422</td><td>Unprocessable Entity</td><td>Validation error or business logic error</td></tr>
        <tr><td>429</td><td>Too Many Requests</td><td>Rate limit exceeded</td></tr>
    </tbody>
</table>

<div class="page-break"></div>
