@extends('layouts.app')

@section('title', 'Customers Directory')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b-2 border-black">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-users text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Customer Directory & Ledger</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Player Profiles, Total Frames & Outstanding Balances
      </p>
    </div>
    <button onclick="document.getElementById('add-customer-box').classList.toggle('hidden')" class="btn-brutal px-4 py-2 bg-brand-yellow hover:bg-yellow-400 text-black text-xs uppercase font-black flex items-center gap-1.5 self-start sm:self-auto">
      <i class="fa-solid fa-user-plus"></i>
      <span>+ Add Customer</span>
    </button>
  </div>

  <!-- Add Customer Form Card (Collapsible) -->
  <div id="add-customer-box" class="hidden card-brutal p-5 bg-yellow-50 border-2 border-black space-y-3">
    <h3 class="text-xs font-black uppercase tracking-wider text-gray-950 flex items-center gap-1.5">
      <i class="fa-solid fa-user-plus"></i>
      <span>Register New Player Profile</span>
    </h3>
    <form action="{{ route('customers.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      @csrf
      <div>
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Customer Name *</label>
        <input type="text" name="name" required placeholder="e.g. Tariq Malik" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
      </div>
      <div>
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Phone Number (optional)</label>
        <input type="tel" name="phone" placeholder="e.g. 0300-9876543" class="w-full input-brutal px-3 py-2 text-xs font-mono text-gray-950">
      </div>
      <div class="flex items-end gap-2">
        <button type="submit" class="btn-brutal px-5 py-2 bg-black text-white text-xs font-black uppercase flex-1">
          Save Player
        </button>
        <button type="button" onclick="document.getElementById('add-customer-box').classList.add('hidden')" class="btn-brutal px-3 py-2 bg-white text-xs font-bold">
          Cancel
        </button>
      </div>
    </form>
  </div>

  <!-- Search Filter -->
  <div class="card-brutal p-4 bg-white">
    <form action="{{ route('customers.index') }}" method="GET" class="flex gap-2">
      <div class="relative flex-1">
        <input 
          type="text" 
          name="search" 
          value="{{ request('search') }}" 
          placeholder="Search by customer name or phone number..." 
          class="w-full input-brutal px-4 py-2 text-xs font-bold text-gray-950"
        >
      </div>
      <button type="submit" class="btn-brutal px-5 py-2 bg-black text-white text-xs uppercase font-black">
        Search
      </button>
      @if(request('search'))
        <a href="{{ route('customers.index') }}" class="btn-brutal px-3 py-2 bg-gray-200 text-xs font-bold">
          Clear
        </a>
      @endif
    </form>
  </div>

  <!-- Customer Ledger Table -->
  <div class="card-brutal bg-white overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b-2 border-black bg-gray-100 text-gray-900 uppercase font-black tracking-wider">
            <th class="p-3.5">Customer Name</th>
            <th class="p-3.5">Phone Number</th>
            <th class="p-3.5">Total Visits</th>
            <th class="p-3.5">Total Frames</th>
            <th class="p-3.5">Total Spent</th>
            <th class="p-3.5">Paid</th>
            <th class="p-3.5">Unpaid Dues</th>
            <th class="p-3.5 text-right">Profile</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-gray-200 font-medium">
          @forelse($customers as $c)
            <tr class="hover:bg-gray-50">
              <td class="p-3.5">
                <a href="{{ route('customers.show', $c->id) }}" class="font-black text-sm text-gray-950 underline hover:text-amber-600 block">
                  {{ $c->name }}
                </a>
              </td>
              <td class="p-3.5 font-mono text-gray-600">
                {{ $c->phone ?? '—' }}
              </td>
              <td class="p-3.5 font-mono font-bold">
                {{ $c->totalSessionsCount() }}
              </td>
              <td class="p-3.5 font-mono">
                {{ $c->totalRoundsCount() }}
              </td>
              <td class="p-3.5 font-mono font-bold text-gray-950">
                {{ $currency }} {{ number_format($c->totalSpent(), 0) }}
              </td>
              <td class="p-3.5 font-mono text-green-700 font-bold">
                {{ $currency }} {{ number_format($c->totalPaid(), 0) }}
              </td>
              <td class="p-3.5 font-mono font-black {{ $c->totalUnpaid() > 0 ? 'text-red-700' : 'text-gray-400' }}">
                {{ $currency }} {{ number_format($c->totalUnpaid(), 0) }}
              </td>
              <td class="p-3.5 text-right">
                <a href="{{ route('customers.show', $c->id) }}" class="btn-brutal px-3 py-1 bg-white hover:bg-gray-100 text-xs font-bold inline-block">
                  View History
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-8 text-center text-gray-500 font-bold">
                No customer records found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($customers->hasPages())
      <div class="p-4 border-t-2 border-black bg-gray-50">
        {{ $customers->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
