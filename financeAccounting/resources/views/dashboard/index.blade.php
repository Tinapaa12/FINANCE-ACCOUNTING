@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="initCharts()" class="space-y-6">
    @include('dashboard._kpi-cards')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-9 space-y-4">
            @include('dashboard._charts')
            @include('dashboard._recent-entries')
        </div>
        @include('dashboard._sidebar')
    </div>
</div>
@endsection

@section('scripts')
    @include('dashboard._script')
@endsection
