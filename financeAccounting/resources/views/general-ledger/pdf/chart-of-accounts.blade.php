@extends('layouts.pdf')

@section('title', 'Chart of Accounts PDF')
@section('company-name', '')
@section('pdf-title', 'Chart of Accounts')

@section('pdf-content')
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b text-left text-gray-500">
                <th class="py-2 px-3 font-medium">Code</th>
                <th class="py-2 px-3 font-medium">Account Name</th>
                <th class="py-2 px-3 font-medium">Type</th>
                <th class="py-2 px-3 font-medium">Normal Balance</th>
                <th class="py-2 px-3 font-medium text-right">Current Balance</th>
                <th class="py-2 px-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
                <tr class="border-b last:border-0">
                    <td class="py-2 px-3">{{ $account->account_code }}</td>
                    <td class="py-2 px-3">{{ $account->account_name }}</td>
                    <td class="py-2 px-3">{{ $account->type }}</td>
                    <td class="py-2 px-3">{{ $account->normal_balance }}</td>
                    <td class="py-2 px-3 text-right">₱{{ number_format($account->current_balance, 2) }}</td>
                    <td class="py-2 px-3">{{ $account->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
