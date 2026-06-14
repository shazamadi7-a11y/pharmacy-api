<h2>10. Code Structure</h2>

<h3>Directory Tree</h3>

<div class="tree">
app/<br>
+-- Console/Commands/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Artisan commands<br>
+-- Enums/<br>
|&nbsp;&nbsp;&nbsp;+-- UserRole.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# customer | admin<br>
|&nbsp;&nbsp;&nbsp;+-- OrderStatus.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# pending | confirmed | rejected | completed<br>
|&nbsp;&nbsp;&nbsp;+-- DosageForm.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# tablet | capsule | syrup | injection | cream | drops<br>
+-- Http/<br>
|&nbsp;&nbsp;&nbsp;+-- Controllers/<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- Controller.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Base controller<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- Api/<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- ApiController.php&nbsp;&nbsp;&nbsp;# Uses ApiResponse trait<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- V1/<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- AuthController.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- CategoryController.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- MedicineController.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- CartController.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- OrderController.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- NotificationController.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-- ReportController.php<br>
|&nbsp;&nbsp;&nbsp;+-- Middleware/<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- ForceJsonResponse.php&nbsp;&nbsp;&nbsp;# Forces Accept: application/json<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- LogApiRequests.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Logs all API requests<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- AdminMiddleware.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Blocks non-admin users (403)<br>
|&nbsp;&nbsp;&nbsp;+-- Requests/Api/V1/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# One FormRequest per endpoint action<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- RegisterRequest.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- LoginRequest.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- StoreCategoryRequest.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- StoreMedicineRequest.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- AddToCartRequest.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- PlaceOrderRequest.php<br>
|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;+-- ... (14 total)<br>
|&nbsp;&nbsp;&nbsp;+-- Resources/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# JSON API resources<br>
+-- Models/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Eloquent models<br>
|&nbsp;&nbsp;&nbsp;+-- User.php, Category.php, Medicine.php, MedicineImage.php<br>
|&nbsp;&nbsp;&nbsp;+-- CartItem.php, Order.php, OrderItem.php<br>
|&nbsp;&nbsp;&nbsp;+-- OtpCode.php, StockLog.php<br>
+-- Notifications/<br>
|&nbsp;&nbsp;&nbsp;+-- OtpNotification.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# OTP code delivery<br>
|&nbsp;&nbsp;&nbsp;+-- OrderStatusChanged.php&nbsp;&nbsp;&nbsp;&nbsp;# Order status change alerts<br>
|&nbsp;&nbsp;&nbsp;+-- LowStockAlert.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Low stock warnings for admin<br>
+-- Services/<br>
|&nbsp;&nbsp;&nbsp;+-- CloudinaryService.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Image upload/delete via Cloudinary<br>
|&nbsp;&nbsp;&nbsp;+-- OtpService.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# OTP generation and verification<br>
+-- Traits/<br>
&nbsp;&nbsp;&nbsp;&nbsp;+-- ApiResponse.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Standardized JSON response helpers<br>
<br>
database/<br>
+-- migrations/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Schema definitions<br>
+-- factories/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# User, Category, Medicine factories<br>
+-- seeders/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Admin, Category, Medicine, Order seeders<br>
<br>
routes/<br>
+-- api/v1.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# All API v1 routes<br>
<br>
tests/Feature/Api/V1/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# Pest PHP feature tests<br>
+-- AuthTest.php, CategoryTest.php, MedicineTest.php<br>
+-- CartTest.php, OrderTest.php, OtpTest.php<br>
+-- NotificationTest.php, ReportTest.php, ReceiptTest.php<br>
+-- EmailVerificationTest.php, PasswordResetTest.php
</div>

<h3>Controller Hierarchy</h3>

<div class="code-block">Controller (base)
  +-- ApiController (uses ApiResponse trait)
       +-- AuthController
       +-- CategoryController
       +-- MedicineController
       +-- CartController
       +-- OrderController
       +-- NotificationController
       +-- ReportController</div>

<h3>Key Services</h3>

<table>
    <thead>
        <tr><th>Service</th><th>Purpose</th></tr>
    </thead>
    <tbody>
        <tr><td><span class="inline-code">CloudinaryService</span></td><td>Handles image upload, deletion, and URL generation via Cloudinary SDK</td></tr>
        <tr><td><span class="inline-code">OtpService</span></td><td>Generates 6-digit OTP codes, validates them, handles expiry</td></tr>
    </tbody>
</table>

<h3>Middleware Stack</h3>

<table>
    <thead>
        <tr><th>Alias</th><th>Class</th><th>Purpose</th></tr>
    </thead>
    <tbody>
        <tr><td><span class="inline-code">force.json</span></td><td>ForceJsonResponse</td><td>Ensures all requests get JSON responses</td></tr>
        <tr><td><span class="inline-code">log.api</span></td><td>LogApiRequests</td><td>Logs API requests for debugging</td></tr>
        <tr><td><span class="inline-code">admin</span></td><td>AdminMiddleware</td><td>Blocks non-admin users with 403</td></tr>
        <tr><td><span class="inline-code">verified</span></td><td>Custom verification</td><td>Checks email verification status</td></tr>
    </tbody>
</table>

<div class="page-break"></div>
