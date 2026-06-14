<h2>9. User Journey Walkthrough</h2>

<p>This section walks through a complete user journey from registration to order completion.</p>

<h3>Step 1: Customer Registers</h3>

<div class="code-block">POST /api/v1/register

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "mypassword123",
  "password_confirmation": "mypassword123",
  "phone": "+970591234567"
}

Response: 201 -- Returns user data + Bearer token
          Verification email sent automatically</div>

<h3>Step 2: Customer Verifies Email</h3>

<div class="code-block"># Check storage/logs/laravel.log for the verification link (dev mode)
POST /api/v1/email/verify/{id}/{hash}
Authorization: Bearer {token}</div>

<h3>Step 3: Customer Browses Medicines</h3>

<div class="code-block"># Browse all categories
GET /api/v1/categories

# Search for painkillers
GET /api/v1/medicines?filter[search]=panadol&filter[is_available]=1

# View medicine details
GET /api/v1/medicines/5</div>

<h3>Step 4: Customer Adds Items to Cart</h3>

<div class="code-block"># Add Panadol (qty: 2)
POST /api/v1/cart
Authorization: Bearer {token}
{"medicine_id": 5, "quantity": 2}

# Add Augmentin (qty: 1)
POST /api/v1/cart
Authorization: Bearer {token}
{"medicine_id": 8, "quantity": 1}

# View cart
GET /api/v1/cart
Authorization: Bearer {token}
-- Shows items, subtotals, and total</div>

<h3>Step 5: Customer Places Order</h3>

<div class="code-block">POST /api/v1/orders
Authorization: Bearer {token}
{"notes": "Please deliver between 2-5 PM"}

Response: 201 -- Order created with status "pending"
          Cart is cleared, stock is deducted</div>

<h3>Step 6: Admin Reviews Orders</h3>

<div class="code-block"># Admin logs in
POST /api/v1/login
{"email": "admin@pharmacy.com", "password": "password"}

# View all pending orders
GET /api/v1/admin/orders
Authorization: Bearer {admin-token}

# View order details
GET /api/v1/admin/orders/1
Authorization: Bearer {admin-token}</div>

<h3>Step 7: Admin Confirms the Order</h3>

<div class="code-block">PATCH /api/v1/admin/orders/1/confirm
Authorization: Bearer {admin-token}
{"admin_notes": "Approved. Ready for pickup tomorrow."}

-- Customer receives a notification</div>

<h3>Step 8: Admin Completes the Order</h3>

<div class="code-block">PATCH /api/v1/admin/orders/1/complete
Authorization: Bearer {admin-token}
{"admin_notes": "Delivered successfully."}

-- Order status changes to "completed"
-- Customer receives a notification</div>

<h3>Step 9: Customer Downloads Receipt</h3>

<div class="code-block">GET /api/v1/orders/1/receipt
Authorization: Bearer {token}

-- Returns PDF file download</div>

<h3>Alternative: Admin Rejects Order</h3>

<div class="code-block">PATCH /api/v1/admin/orders/1/reject
Authorization: Bearer {admin-token}
{"admin_notes": "Prescription required but not provided."}

-- Stock is restored automatically
-- Customer receives a notification</div>

<div class="page-break"></div>
