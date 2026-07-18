<div x-show="selectedEntry" x-transition class="grid grid-cols-12 gap-6">
    @include('general-ledger.journal-entries._details')
    @include('general-ledger.journal-entries._lines')
    @include('general-ledger.journal-entries._summary')
</div>
