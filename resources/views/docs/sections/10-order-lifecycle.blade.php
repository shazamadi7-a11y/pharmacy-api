<h2>8. Order Lifecycle</h2>

<p>Orders follow a strict state machine with four possible statuses, defined in <span class="inline-code">App\Enums\OrderStatus</span>:</p>

<h3>Status Flow</h3>

<div class="lifecycle-box">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+----------+<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;| PENDING  |<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+----+-----+<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+------+------+<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+----v----+&nbsp;&nbsp;+----v-----+<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|CONFIRMED|&nbsp;&nbsp;| REJECTED |<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+----+----+&nbsp;&nbsp;+----------+<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(stock restored)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+----v-----+<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|COMPLETED |<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+----------+
</div>

<h3>Valid Transitions</h3>

<table>
    <thead>
        <tr><th>From</th><th>To</th><th>Endpoint</th><th>Side Effect</th></tr>
    </thead>
    <tbody>
        <tr>
            <td>Pending</td>
            <td>Confirmed</td>
            <td><span class="inline-code">PATCH /admin/orders/{id}/confirm</span></td>
            <td>Customer notified</td>
        </tr>
        <tr>
            <td>Pending</td>
            <td>Rejected</td>
            <td><span class="inline-code">PATCH /admin/orders/{id}/reject</span></td>
            <td>Stock quantities restored, customer notified</td>
        </tr>
        <tr>
            <td>Confirmed</td>
            <td>Completed</td>
            <td><span class="inline-code">PATCH /admin/orders/{id}/complete</span></td>
            <td>Customer notified</td>
        </tr>
    </tbody>
</table>

<div class="warning-box">
    <strong>Invalid transitions</strong> return a 422 error. For example, you cannot confirm an already rejected order, or complete a pending order directly.
</div>

<h3>Stock Management</h3>

<table>
    <thead>
        <tr><th>Event</th><th>Stock Action</th><th>Stock Log Type</th></tr>
    </thead>
    <tbody>
        <tr>
            <td>Order placed</td>
            <td>Stock decremented for each item</td>
            <td><span class="inline-code">sale</span></td>
        </tr>
        <tr>
            <td>Order rejected</td>
            <td>Stock restored (incremented back)</td>
            <td><span class="inline-code">restock</span></td>
        </tr>
        <tr>
            <td>Medicine stock falls below threshold</td>
            <td>Low stock alert notification sent to admin</td>
            <td>&mdash;</td>
        </tr>
    </tbody>
</table>

<h3>Order Placement Process (Transaction)</h3>

<p>When a customer places an order (<span class="inline-code">POST /api/v1/orders</span>), the following happens atomically inside a database transaction:</p>

<ol>
    <li>Validate cart is not empty</li>
    <li>Validate all medicines are available and have sufficient stock</li>
    <li>Create the order record with calculated total price</li>
    <li>Create order items (snapshot unit_price from medicine's current price)</li>
    <li>Decrement stock_quantity for each medicine</li>
    <li>Create stock log entries (type: sale)</li>
    <li>Clear the customer's cart</li>
    <li>Send low stock alert if any medicine drops below threshold</li>
</ol>

<div class="info-box">
    <strong>Price Snapshot:</strong> The <span class="inline-code">unit_price</span> in order_items is captured at order time. If a medicine's price changes later, existing orders are unaffected.
</div>

<div class="page-break"></div>
