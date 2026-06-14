# Changelog

All notable changes to this project will be documented in this file.

## [3.0.0] (2026-03-08)

### Features

- Add Cloudinary image upload integration for categories and medicines
- Add `CloudinaryService` for upload/delete operations with lazy initialization
- Add `DELETE /medicines/{id}/images/{imageId}` endpoint for individual image deletion
- Add `MedicineSeeder` with 17 realistic medicines across 6 categories with placeholder images
- Add `OrderSeeder` with 6 sample orders in mixed statuses
- Update `CategorySeeder` with placeholder image URLs from placehold.co
- Update category/medicine validation to accept image file uploads (max 2MB)
- Support up to 5 image uploads per medicine (`images[]` array field)

### Configuration

- Add `config/cloudinary.php` for Cloudinary credentials
- Add `CLOUDINARY_*` environment variables to `.env.example`

### Dependencies

- Add `cloudinary/cloudinary_php` ^3.1

### Bug Fixes

- Fix PHPStan error in `AppServiceProvider` (Scramble SecurityScheme type)

## [2.2.0](https://github.com/Grazulex/laravel-api-kit/releases/tag/v2.2.0) (2026-01-29)

### Features

- add email verification endpoints with signed URLs (#16)
- add password reset flow with secure tokens (#16)
- add 3 reusable API middleware: ForceJsonResponse, LogApiRequests, EnsureEmailVerified (#16)

### Documentation

- add documentation for email verification, password reset and middleware (#17)

## [2.1.0](https://github.com/Grazulex/laravel-api-kit/releases/tag/v2.1.0) (2026-01-29)

### Features

- integrate PHPStan, Rector and Pint code quality tools (#13)

### Chores

- **release:** 2.1.0 (#14)
## [2.0.2](https://github.com/Grazulex/laravel-api-kit/releases/tag/v2.0.2) (2026-01-08)

### Bug Fixes

- clean up routes after merge conflict ([fcf6483](https://github.com/Grazulex/laravel-api-kit/commit/fcf64831272a1aaf20fde0d8001714994ccc6932))

### Chores

- update dependencies and fix apiroute config (#10) ([2b0f6ce](https://github.com/Grazulex/laravel-api-kit/commit/2b0f6cedaa2e7804d0a2a30c0d8c8d0dc27c2f57))
## [1.1.0](https://github.com/Grazulex/laravel-api-kit/releases/tag/v1.1.0) (2025-12-25)

### Chores

- **deps:** update laravel-apiroute to ^1.0 ([02c55f7](https://github.com/Grazulex/laravel-api-kit/commit/02c55f765f103852258398d7d0d0790146a33f10))
## [1.0.0](https://github.com/Grazulex/laravel-api-kit/releases/tag/v1.0.0) (2025-12-25)

### Features

- Initial Laravel API-only starter kit ([5900990](https://github.com/Grazulex/laravel-api-kit/commit/5900990527dfdc0b38cb17f06106728e21fb7cc6))
