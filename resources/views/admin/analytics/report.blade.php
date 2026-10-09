@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.analytics') }}" class="text-secondary text-decoration-none">Back to Analytics</a>
    <h1 class="h3 mt-3">{{ $title }} Report</h1>
    <p class="text-muted">Last 30 days</p>
</div>
<div class="card border shadow-sm p-4 mb-4">
    @if($metric === null)
        <p class="mb-0 text-muted">Session tracking is not configured for this store.</p>
    @else
        <div class="h2 mb-0">{{ $money ? ($currentTenant->currency ?? '₹') : '' }}{{ number_format($metric, $money ? 2 : 0) }}</div>
    @endif
</div>
@if($metric !== null)
<div class="card border shadow-sm">
    <div class="card-header">Daily Breakdown</div>
    <table class="table mb-0">
        <thead><tr><th>Date</th><th class="text-end">{{ $title }}</th></tr></thead>
        <tbody>
        @forelse($daily as $row)
            <tr><td>{{ $row['date'] }}</td><td class="text-end">{{ $money ? ($currentTenant->currency ?? '₹') : '' }}{{ number_format($row['value'], $money ? 2 : 0) }}</td></tr>
        @empty
            <tr><td colspan="2" class="text-muted">No orders in this period.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endif
@endsection
