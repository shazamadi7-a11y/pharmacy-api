<h2>7. Roles &amp; Permissions</h2>

<p>The system uses two roles defined in the <span class="inline-code">App\Enums\UserRole</span> enum:</p>

<table>
    <thead>
        <tr><th>Role</th><th>Value</th><th>Description</th></tr>
    </thead>
    <tbody>
        <tr><td><strong>Customer</strong></td><td><span class="inline-code">customer</span></td><td>Default role. Can browse, manage cart, place and view own orders</td></tr>
        <tr><td><strong>Admin</strong></td><td><span class="inline-code">admin</span></td><td>Full access. Manages categories, medicines, processes orders, views reports</td></tr>
    </tbody>
</table>

<h3>Access Matrix</h3>

<table>
    <thead>
        <tr>
            <th>Feature</th>
            <th style="text-align: center;">Public</th>
            <th style="text-align: center;">Customer</th>
            <th style="text-align: center;">Admin</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Browse categories &amp; medicines</td><td style="text-align: center;">Yes</td><td style="text-align: center;">Yes</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>Register / Login</td><td style="text-align: center;">Yes</td><td style="text-align: center;">&mdash;</td><td style="text-align: center;">&mdash;</td></tr>
        <tr><td>Manage cart</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>Place orders</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>View own orders</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>Download own receipt</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>Create/Edit/Delete categories</td><td style="text-align: center;">No</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>Create/Edit/Delete medicines</td><td style="text-align: center;">No</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>View all orders</td><td style="text-align: center;">No</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>Confirm/Reject/Complete orders</td><td style="text-align: center;">No</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>View reports</td><td style="text-align: center;">No</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td></tr>
        <tr><td>Low stock alerts</td><td style="text-align: center;">No</td><td style="text-align: center;">No</td><td style="text-align: center;">Yes</td></tr>
    </tbody>
</table>

<h3>How It Works</h3>

<ul>
    <li>New users are assigned the <span class="inline-code">customer</span> role by default</li>
    <li>The <span class="inline-code">AdminMiddleware</span> protects admin-only routes</li>
    <li>Non-admin users receive a <strong>403</strong> response: <span class="inline-code">{"success": false, "message": "Admin access required"}</span></li>
</ul>

<h3>Route Protection Layers</h3>

<div class="code-block">Public routes          --&gt; No auth required
Auth routes            --&gt; auth:sanctum middleware
Admin routes           --&gt; auth:sanctum + admin middleware</div>

<h3>User Model Helpers</h3>

<div class="code-block">$user->isAdmin();      // true if role === UserRole::Admin
$user->isCustomer();   // true if role === UserRole::Customer
$user->role;           // Returns UserRole enum instance
$user->role->value;    // Returns "admin" or "customer"</div>

<div class="page-break"></div>
