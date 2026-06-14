<h2>4. Database Schema</h2>

<p>The application uses 10 database tables. SQLite is used for development and testing; MySQL/PostgreSQL for production.</p>

@foreach($schema as $table)
<div class="avoid-break">
    <h3>4.{{ $loop->iteration }} <span class="inline-code">{{ $table['table'] }}</span></h3>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Column</th>
                <th style="width: 20%;">Type</th>
                <th>Constraints</th>
            </tr>
        </thead>
        <tbody>
            @foreach($table['columns'] as $col)
            <tr>
                <td><span class="inline-code">{{ $col['name'] }}</span></td>
                <td>{{ $col['type'] }}</td>
                <td>{{ $col['constraints'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

<h3>4.11 Entity Relationship Diagram</h3>

<div class="erd-box">
users --&lt; cart_items &gt;-- medicines --&lt; medicine_images<br>
&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;+--&lt; orders --&lt; order_items --+<br>
&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+--&lt; stock_logs<br>
&nbsp;&nbsp;+--&lt; otp_codes<br>
&nbsp;&nbsp;+--&lt; notifications<br>
&nbsp;&nbsp;|<br>
&nbsp;&nbsp;+-- categories --&lt; medicines
</div>

<div class="info-box">
    <strong>Key relationships:</strong>
    <ul>
        <li>A <strong>User</strong> has many Cart Items, Orders, OTP Codes, and Notifications</li>
        <li>A <strong>Category</strong> has many Medicines (cascade delete)</li>
        <li>A <strong>Medicine</strong> has many Images, Cart Items, and Order Items</li>
        <li>An <strong>Order</strong> has many Order Items and Stock Logs</li>
        <li>Order Items use <strong>restrict delete</strong> on medicine_id (cannot delete medicine with orders)</li>
        <li>The <span class="inline-code">unit_price</span> in order_items is a snapshot &mdash; future price changes don't affect past orders</li>
    </ul>
</div>

<div class="page-break"></div>
