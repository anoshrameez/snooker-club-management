@extends('layouts.app')

@section('title', 'Customer Profile — ' . $customer->name)

@section('content')
<div class="space-y-6">

  <!-- Header & Back Button -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b-2 border-black">
    <div class="flex items-center gap-3">
      <a href="{{ route('customers.index') }}" class="btn-brutal px-3 py-1.5 bg-white text-xs uppercase font-bold">
        ← Customers
      </a>
      <div>
        <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950">
          {{ $customer->name }}
        </h1>
        <p class="text-xs font-mono font-bold text-gray-500">
          {{ $customer->phone ?? 'No phone registered' }} • Member since {{ $customer->created_at->format('M Y') }}
        </p>
      </div>
    </div>
    <a href="{{ route('sessions.create') }}?customer_name={{ urlencode($customer->name) }}" class="btn-brutal px-5 py-2.5 bg-brand-yellow hover:bg-yellow-400 text-black text-xs uppercase font-black flex items-center gap-1.5 self-start sm:self-auto">
      <i class="fa-solid fa-play"></i>
      <span>+ New Session For Player</span>
    </a>
  </div>

  <!-- STATS CARDS GRID (SECTION 17) -->
  <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
    <div class="card-brutal p-4 bg-white">
      <p class="text-[10px] font-black uppercase text-gray-500 tracking-wider">Total Games</p>
      <p class="text-2xl font-black text-gray-950 font-mono mt-1">{{ $customer->totalSessionsCount() }}</p>
    </div>

    <div class="card-brutal p-4 bg-white">
      <p class="text-[10px] font-black uppercase text-gray-500 tracking-wider">Total Frames</p>
      <p class="text-2xl font-black text-gray-950 font-mono mt-1">{{ $customer->totalRoundsCount() }}</p>
    </div>

    <div class="card-brutal p-4 bg-white">
      <p class="text-[10px] font-black uppercase text-gray-500 tracking-wider">Total Spent</p>
      <p class="text-2xl font-black text-gray-950 font-mono mt-1">
        {{ $currency }} {{ number_format($customer->totalSpent(), 0) }}
      </p>
    </div>

    <div class="card-brutal p-4 bg-green-50 border-green-700">
      <p class="text-[10px] font-black uppercase text-green-800 tracking-wider">Paid Amount</p>
      <p class="text-2xl font-black text-green-950 font-mono mt-1">
        {{ $currency }} {{ number_format($customer->totalPaid(), 0) }}
      </p>
    </div>

    <div class="card-brutal p-4 bg-red-50 border-red-700 col-span-2 md:col-span-1">
      <p class="text-[10px] font-black uppercase text-red-800 tracking-wider">Outstanding Dues</p>
      <p class="text-2xl font-black text-red-950 font-mono mt-1">
        {{ $currency }} {{ number_format($customer->totalUnpaid(), 0) }}
      </p>
    </div>
  </div>

  <!-- CUSTOMER'S COMPLETE SESSIONS HISTORY -->
  <div class="card-brutal bg-white overflow-hidden space-y-3 p-5">
    <h3 class="text-xs font-black uppercase tracking-wider text-gray-950 border-b-2 border-black pb-2 flex items-center gap-2">
      <i class="fa-solid fa-clock-rotate-left"></i>
      <span>Player's Session Records ({{ $customer->gameSessions->count() }})</span>
    </h3>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b-2 border-black bg-gray-100 text-gray-900 uppercase font-black tracking-wider">
            <th class="p-3">Session</th>
            <th class="p-3">Date</th>
            <th class="p-3">Table</th>
            <th class="p-3">Frames</th>
            <th class="p-3">Duration</th>
            <th class="p-3">Total Amount</th>
            <th class="p-3">Payment</th>
            <th class="p-3 text-right">View</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-gray-200 font-medium">
          @forelse($customer->gameSessions as $s)
            <tr class="hover:bg-gray-50">
              <td class="p-3 font-mono font-bold">{{ $s->session_code }}</td>
              <td class="p-3">
                <span class="font-bold text-gray-900">{{ $s->start_time->format('d M Y') }}</span>
                <span class="text-gray-400 block font-mono text-[10px]">{{ $s->start_time->format('h:i A') }}</span>
              </td>
              <td class="p-3 font-bold uppercase">{{ $s->table->name ?? 'Table' }}</td>
              <td class="p-3 font-mono font-bold">{{ $s->rounds }}</td>
              <td class="p-3 font-mono text-gray-600">{{ $s->formattedDuration() }}</td>
              <td class="p-3 font-mono font-black text-gray-950">{{ $currency }} {{ number_format($s->total_price, 0) }}</td>
              <td class="p-3">
                <span class="badge-brutal px-2 py-0.5 text-[10px] {{ $s->isPaid() ? 'bg-green-100 text-green-950 border-green-700' : 'bg-red-100 text-red-950 border-red-700' }}">
                  {{ strtoupper($s->payment_status) }}
                </span>
              </td>
              <td class="p-3 text-right">
                <a href="{{ route('sessions.show', $s->id) }}" class="btn-brutal px-2.5 py-1 bg-white hover:bg-gray-100 text-xs font-bold">
                  {{ $s->isActive() ? 'Open' : 'Receipt' }}
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-6 text-center text-gray-500 font-bold">
                No past sessions recorded for this customer yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
