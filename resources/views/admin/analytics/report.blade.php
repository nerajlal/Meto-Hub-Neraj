@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.analytics') }}" class="text-secondary hover-text-dark text-decoration-none small mb-3 d-inline-block">
        <i class="fas fa-arrow-left me-1"></i> Back to Analytics
    </a>
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0 text-primary">{{ $title }} Report</h1>
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2 border rounded bg-white px-3 py-1 shadow-sm small cursor-pointer hover-bg-light dropdown-toggle" data-bs-toggle="dropdown">
                <i class="far fa-calendar text-secondary"></i>
                <span class="text-dark">Last 30 days</span>
            </div>
            <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end">
                <li><a class="dropdown-item small" href="{{ route('admin.analytics', ['period' => '7_days']) }}">Last 7 days</a></li>
                <li><a class="dropdown-item small active bg-light text-dark fw-bold" href="{{ route('admin.analytics', ['period' => '30_days']) }}">Last 30 days</a></li>
                <li><a class="dropdown-item small" href="{{ route('admin.analytics', ['period' => '90_days']) }}">Last 90 days</a></li>
                <li><a class="dropdown-item small" href="{{ route('admin.analytics', ['period' => 'year']) }}">Last Year</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="card bg-white border shadow-sm p-4 mb-4">
    <div class="d-flex align-items-end gap-3 mb-4">
        <div class="h2 fw-bold text-dark mb-0 opacity-50 display-6">{{ $value }}</div>
        <div class="small text-success fw-medium mb-1">
             <i class="fas fa-arrow-up me-1"></i> 12%
             <span class="text-muted fw-normal ms-1">vs previous period</span>
        </div>
    </div>

    <!-- Big Chart Placeholder -->
    <div class="bg-light rounded border d-flex align-items-end justify-content-between px-3 pb-0 position-relative overflow-hidden" style="height: 250px;">
        <!-- Bars -->
        @for ($i = 0; $i < 30; $i++)
            @php $h = rand(30, 95); @endphp
            <div class="w-100 bg-primary opacity-75 hover-opacity-100 mx-1 rounded-top" style="height: {{ $h }}%; transition: opacity 0.2s;"></div>
        @endfor
    </div>
</div>

<!-- Detailed Data Table -->
<div class="card bg-white border shadow-sm overflow-hidden">
    <div class="card-header bg-light border-bottom p-3">
        <h3 class="h6 fw-semibold text-secondary mb-0">Daily Breakdown</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-start">
            <thead class="bg-light text-uppercase small fw-medium text-muted border-bottom">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3 text-end">{{ $metricLabel }}</th>
                    <th class="px-4 py-3 text-end">Growth</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @for ($i = 1; $i <= 5; $i++)
                <tr>
                    <td class="px-4 py-3">Dec {{ 31 - $i }}, 2025</td>
                    <td class="px-4 py-3 text-end fw-medium text-dark">{{ $i % 2 == 0 ? '{{ $currentTenant->currency ?? '₹' }}1,200.00' : '150' }}</td>
                    <td class="px-4 py-3 text-end text-success">+5%</td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
<style>
    .hover-text-dark:hover { color: #212529 !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .hover-opacity-100:hover { opacity: 1 !important; }
</style>
@endsection
