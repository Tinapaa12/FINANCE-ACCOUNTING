@extends('layouts.app')

@section('title', 'Journal Entry')
@section('page-title', 'Journal Entry')

@section('content')
<div x-data="journalEntries()" x-init="init()">
    @include('general-ledger.journal-entries._toolbar')
    @include('general-ledger.journal-entries._table')
    @include('general-ledger.journal-entries._details-grid')
    @include('general-ledger.journal-entries._add-modal')
    <x-success-modal show="showSuccessModal" />
</div>
@endsection

@section('scripts')
    @include('general-ledger.journal-entries._script')
@endsection

