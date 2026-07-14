@extends('layouts.app')

@section('title', 'Charts of Accounts')
@section('page-title', 'Charts of Accounts')

@section('content')
<div x-data="chartOfAccounts()" x-init="init()">
    @include('general-ledger.chart-of-accounts._toolbar')
    @include('general-ledger.chart-of-accounts._table')
    @include('general-ledger.chart-of-accounts._details')
    @include('general-ledger.chart-of-accounts._add-modal')
    <x-success-modal show="showSuccessModal" />
</div>
@endsection

@section('scripts')
    @include('general-ledger.chart-of-accounts._script')
@endsection