@extends('layouts.app')

@section('title', 'Sessions History')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b-2 border-black">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-clock-rotate-left text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Game Sessions History</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Complete Record of Frames, Billing & Payment Statuses
      </p>
    </div>
    <a href="{{ route('sessions.create') }}" class="btn-brutal px-5 py-2.5 bg-brand-yellow hover:bg-yellow-400 text-black text-xs uppercase font-black flex items-center gap-1.5 self-start sm:self-auto">
      <i class="fa-solid fa-plus"></i>
      <span>+ NEW SESSION</span>
    </a>
  </div>

  <!-- FILTER CONTROLS BAR -->
  <div class="card-brutal p-5 bg-white space-y-4">
    <form action="{{ route('sessions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
      
      <!-- Search Input -->
      <div class="lg:col-span-2">
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Search Player / Phone / Code</label>
        <div class="relative">
          <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="e.g. Ahmed or SESS-1001" 
            class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950"
          >
        </div>
      </div>

      <!-- Table Filter -->
      <div>
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Filter by Table</label>
        <select name="table_id" class="w-full input-brutal px-2.5 py-2 text-xs font-bold text-gray-950">
          <option value="">All Tables</option>
          @foreach($tables as $tbl)
            <option value="{{ $tbl->id }}" {{ request('table_id') == $tbl->id ? 'selected' : '' }}>
              {{ $tbl->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Payment Filter -->
      <div>
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Payment Status</label>
        <select name="payment_status" class="w-full input-brutal px-2.5 py-2 text-xs font-bold text-gray-950">
          <option value="">All Payments</option>
          <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>PAID</option>
          <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>UNPAID</option>
        </select>
      </div>

      <!-- Date From -->
      <div>
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Date From</label>
        <input 
          type="date" 
          name="date_from" 
          value="{{ request('date_from') }}" 
          class="w-full input-brutal px-2.5 py-2 text-xs font-mono text-gray-950"
        >
      </div>

      <!-- Actions -->
      <div class="flex items-end gap-2">
        <button type="submit" class="btn-brutal flex-1 py-2 bg-black text-white text-xs uppercase font-black hover:bg-gray-900">
          Filter
        </button>
        <a href="{{ route('sessions.index') }}" class="btn-brutal px-3 py-2 bg-gray-200 hover:bg-gray-300 text-xs font-bold" title="Reset Filters">
          ✕
        </a>
      </div>

    </form>
  </div>

  <!-- SESSIONS TABLE (SECTION 15) -->
  <div class="card-brutal bg-white overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b-2 border-black bg-gray-100 text-gray-900 uppercase font-black tracking-wider">
            <th class="p-3.5">Code</th>
            <th class="p-3.5">Date & Time</th>
            <th class="p-3.5">Customer</th>
            <th class="p-3.5">Table</th>
            <th class="p-3.5">Gameplay</th>
            <th class="p-3.5">Frames / Time</th>
            <th class="p-3.5">Duration</th>
            <th class="p-3.5">Total (Cash)</th>
            <th class="p-3.5">Payment</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Receipt / Details</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-gray-200 font-medium">
          @forelse($sessions as $sess)
            <tr class="hover:bg-yellow-50/50 transition">
              <td class="p-3.5 font-mono font-bold text-gray-950">
                <a href="{{ route('sessions.show', $sess->id) }}" class="underline hover:text-amber-600">
                  {{ $sess->session_code }}
                </a>
              </td>
              <td class="p-3.5">
                <span class="font-bold text-gray-900 block">{{ $sess->start_time->format('d M') }}</span>
                <span class="font-mono text-gray-500 text-[11px]">{{ $sess->start_time->format('h:i A') }}</span>
              </td>
              <td class="p-3.5">
                <span class="font-bold text-gray-950 text-sm block">{{ $sess->customer->name ?? 'Guest' }}</span>
                @if($sess->customer && $sess->customer->phone)
                  <span class="font-mono text-gray-500 text-[10px]">{{ $sess->customer->phone }}</span>
                @endif
              </td>
              <td class="p-3.5 font-bold uppercase text-gray-900">
                {{ $sess->table->name ?? 'Table' }}
              </td>
              <td class="p-3.5">
                <span class="badge-brutal px-2 py-0.5 text-[9px] bg-black text-brand-yellow font-black">
                  {{ $sess->gameTitle() }}
                </span>
              </td>
              <td class="p-3.5 font-mono font-bold text-sm">
                @if($sess->isTimeBased())
                  {{ $sess->elapsedMinutes() }} min
                @else
                  {{ $sess->rounds }} frame(s)
                @endif
              </td>
              <td class="p-3.5 font-mono text-gray-700">
                {{ $sess->formattedDuration() }}
              </td>
              <td class="p-3.5 font-mono font-black text-sm text-gray-950">
                {{ $currency }} {{ number_format($sess->total_price, 0) }}
              </td>
              <td class="p-3.5">
                <span class="badge-brutal px-2.5 py-0.5 text-[10px] {{ $sess->isPaid() ? 'bg-green-100 text-green-950 border-green-700' : 'bg-red-100 text-red-950 border-red-700' }}">
                  {{ strtoupper($sess->payment_status) }}
                </span>
              </td>
              <td class="p-3.5">
                @if($sess->isActive())
                  <span class="badge-brutal px-2 py-0.5 bg-yellow-300 text-black text-[10px] animate-pulse">
                    IN PLAY
                  </span>
                @elseif($sess->isCancelled())
                  <span class="badge-brutal px-2 py-0.5 bg-gray-200 text-gray-700 text-[10px]">
                    CANCELLED
                  </span>
                @else
                  <span class="badge-brutal px-2 py-0.5 bg-gray-100 text-gray-800 text-[10px]">
                    DONE
                  </span>
                @endif
              </td>
              <td class="p-3.5 text-right">
                <a href="{{ route('sessions.show', $sess->id) }}" class="btn-brutal px-3 py-1 bg-white hover:bg-gray-100 text-xs font-bold inline-block">
                  {{ $sess->isActive() ? 'Manage' : 'View' }}
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="p-8 text-center text-gray-500 font-bold">
                No matching sessions found in history.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($sessions->hasPages())
      <div class="p-4 border-t-2 border-black bg-gray-50">
        {{ $sessions->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
