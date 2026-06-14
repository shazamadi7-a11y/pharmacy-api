<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pharmacy API Documentation</title>
    <style>
        /* === Base Styles === */
        @page {
            margin: 60px 50px 80px 50px;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* === Page Footer with Page Numbers === */
        .page-footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
        }

        /* === Typography === */
        h1 {
            font-size: 28px;
            color: #1a365d;
            margin: 0 0 10px 0;
            padding: 0;
        }
        h2 {
            font-size: 20px;
            color: #1a365d;
            border-bottom: 2px solid #3182ce;
            padding-bottom: 6px;
            margin: 25px 0 15px 0;
        }
        h3 {
            font-size: 15px;
            color: #2d3748;
            margin: 18px 0 8px 0;
        }
        h4 {
            font-size: 12px;
            color: #4a5568;
            margin: 12px 0 6px 0;
        }
        p {
            margin: 6px 0;
        }

        /* === Tables === */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px 0;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #cbd5e0;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #edf2f7;
            font-weight: bold;
            color: #2d3748;
        }
        tr:nth-child(even) td {
            background-color: #f7fafc;
        }

        /* === Code Blocks === */
        .code-block {
            background-color: #1a202c;
            color: #e2e8f0;
            padding: 10px 12px;
            margin: 8px 0;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            line-height: 1.4;
            border-radius: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .inline-code {
            background-color: #edf2f7;
            color: #c53030;
            padding: 1px 4px;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            border-radius: 2px;
        }

        /* === Method Badges === */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-get { background-color: #38a169; }
        .badge-post { background-color: #3182ce; }
        .badge-put { background-color: #dd6b20; }
        .badge-delete { background-color: #e53e3e; }
        .badge-patch { background-color: #805ad5; }

        /* === Auth/Role Badges === */
        .badge-auth { background-color: #d69e2e; font-size: 8px; }
        .badge-public { background-color: #718096; font-size: 8px; }
        .badge-admin { background-color: #e53e3e; font-size: 8px; }
        .badge-customer { background-color: #38a169; font-size: 8px; }

        /* === Endpoint Card === */
        .endpoint-card {
            border: 1px solid #e2e8f0;
            margin: 10px 0;
            padding: 10px 12px;
            border-radius: 4px;
            background-color: #fff;
        }
        .endpoint-header {
            margin-bottom: 6px;
        }
        .endpoint-path {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 11px;
            font-weight: bold;
            color: #2d3748;
            margin-left: 6px;
        }

        /* === Status Badge === */
        .status-badge {
            display: inline-block;
            padding: 1px 6px;
            font-size: 8px;
            font-weight: bold;
            color: #fff;
            border-radius: 3px;
            background-color: #38a169;
        }

        /* === Info Box === */
        .info-box {
            background-color: #ebf8ff;
            border-left: 4px solid #3182ce;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 10px;
        }
        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #d69e2e;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 10px;
        }

        /* === Section Breaks === */
        .page-break {
            page-break-after: always;
        }
        .avoid-break {
            page-break-inside: avoid;
        }

        /* === Cover Page === */
        .cover-page {
            text-align: center;
            padding-top: 200px;
        }
        .cover-title {
            font-size: 36px;
            color: #1a365d;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .cover-subtitle {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 30px;
        }
        .cover-meta {
            font-size: 13px;
            color: #718096;
            margin: 5px 0;
        }
        .cover-line {
            width: 200px;
            height: 3px;
            background-color: #3182ce;
            margin: 25px auto;
        }

        /* === TOC === */
        .toc-item {
            margin: 4px 0;
            font-size: 12px;
        }
        .toc-section-number {
            display: inline-block;
            width: 30px;
            font-weight: bold;
            color: #3182ce;
        }

        /* === ERD Diagram === */
        .erd-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin: 10px 0;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            line-height: 1.3;
        }

        /* === Lifecycle Diagram === */
        .lifecycle-box {
            background-color: #f0fff4;
            border: 1px solid #c6f6d5;
            padding: 12px;
            margin: 10px 0;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            line-height: 1.3;
        }

        /* === Directory Tree === */
        .tree {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            line-height: 1.4;
            background-color: #f7fafc;
            padding: 10px;
            border: 1px solid #e2e8f0;
        }

        /* === Lists === */
        ul, ol {
            margin: 6px 0;
            padding-left: 20px;
        }
        li {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    {{-- Page Footer --}}
    <div class="page-footer">
        Pharmacy API Documentation v{{ $version }} | Generated {{ $generatedAt }} | Page <script type="text/php">
            if (isset($pdf)) {
                $x = 490;
                $y = 778;
                $text = "{PAGE_NUM} of {PAGE_COUNT}";
                $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                $size = 8;
                $color = array(0.6, 0.6, 0.6);
                $pdf->page_text($x, $y, $text, $font, $size, $color);
            }
        </script>
    </div>

    {{-- Section 1: Cover Page --}}
    @include('docs.sections.01-cover')

    {{-- Section 2: Table of Contents --}}
    @include('docs.sections.02-table-of-contents')

    {{-- Section 3: Project Overview --}}
    @include('docs.sections.03-project-overview')

    {{-- Section 4: Setup Guide --}}
    @include('docs.sections.04-setup-guide')

    {{-- Section 5: Environment Variables --}}
    @include('docs.sections.05-environment-variables')

    {{-- Section 6: Database Schema --}}
    @include('docs.sections.06-database-schema')

    {{-- Section 7: Authentication --}}
    @include('docs.sections.07-authentication')

    {{-- Section 8: API Endpoints --}}
    @include('docs.sections.08-api-endpoints')

    {{-- Section 9: Roles & Permissions --}}
    @include('docs.sections.09-roles-and-permissions')

    {{-- Section 10: Order Lifecycle --}}
    @include('docs.sections.10-order-lifecycle')

    {{-- Section 11: User Journey --}}
    @include('docs.sections.11-user-journey')

    {{-- Section 12: Code Structure --}}
    @include('docs.sections.12-code-structure')

    {{-- Section 13: Testing & Extending --}}
    @include('docs.sections.13-testing-and-extending')
</body>
</html>
