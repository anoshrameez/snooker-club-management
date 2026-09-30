@extends('layouts.app')

@section('title', 'Session Receipt #' . $session->session_code)

@section('content')
<div class="max-w-xl mx-auto space-y-6">

  <!-- Breadcrumbs & Actions -->
  <div class="flex items-center justify-between border-b-2 border-black pb-3 no-print">
    <div class="flex items-center gap-2">
      <a href="{{ route('sessions.index') }}" class="btn-brutal px-3 py-1 bg-white text-xs uppercase font-bold">
        ← All Sessions
      </a>
      <span class="text-xs font-bold text-gray-500">Receipt #{{ $session->session_code }}</span>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="btn-brutal px-4 py-1.5 bg-black text-white text-xs uppercase font-black flex items-center gap-1.5">
        <i class="fa-solid fa-print"></i>
        <span>Print Receipt</span>
      </button>
      <a href="{{ route('sessions.create') }}" class="btn-brutal px-3 py-1.5 bg-brand-yellow text-black text-xs uppercase font-bold">
        + New Game
      </a>
    </div>
  </div>

  <!-- PRINTABLE THERMAL RECEIPT CARD -->
  <div id="receipt-card" class="card-brutal p-8 bg-white border-3 border-black shadow-brutal-lg font-mono text-sm space-y-5">
    
    <!-- Club Header -->
    <div class="text-center border-b-2 border-dashed border-gray-400 pb-4">
      <div class="w-12 h-12 bg-brand-yellow border-2 border-black inline-flex items-center justify-center text-xl font-black mb-2 shadow-brutal-sm">
        🎱
      </div>
      <h2 class="text-lg font-black uppercase text-gray-950 font-sans tracking-tight">{{ $clubName }}</h2>
      <p class="text-xs text-gray-600 uppercase font-sans">Official Snooker Club Receipt</p>
      @if($clubPhone)
        <p class="text-[11px] text-gray-500 font-sans">{{ $clubPhone }}</p>
      @endif
      <div class="mt-2 text-[11px] font-bold text-gray-500 uppercase">
        {{ $session->end_time ? $session->end_time->format('d M Y — h:i A') : $session->start_time->format('d M Y — h:i A') }} (PKT)
      </div>
    </div>

    <!-- Session Details Table -->
    <div class="space-y-2.5 text-xs border-b-2 border-dashed border-gray-400 pb-4">
      <div class="flex justify-between items-center">
        <span class="text-gray-500 uppercase">RECEIPT NO:</span>
        <span class="font-bold text-black text-sm">{{ $session->session_code }}</span>
      </div>

      <div class="flex justify-between items-center">
        <span class="text-gray-500 uppercase">CUSTOMER:</span>
        <span class="font-bold text-black text-sm uppercase">{{ $session->customer->name ?? 'Guest' }}</span>
      </div>

      @if($session->customer && $session->customer->phone)
        <div class="flex justify-between items-center">
          <span class="text-gray-500 uppercase">PHONE:</span>
          <span class="text-gray-800">{{ $session->customer->phone }}</span>
        </div>
      @endif

      <div class="flex justify-between items-center">
        <span class="text-gray-500 uppercase">TABLE:</span>
        <span class="font-bold text-black uppercase">{{ $session->table->name ?? 'Table' }}</span>
      </div>

      <div class="flex justify-between items-center">
        <span class="text-gray-500 uppercase">GAMEPLAY:</span>
        <span class="font-black text-black uppercase">{{ $session->gameTitle() }}</span>
      </div>

      <div class="flex justify-between items-center">
        <span class="text-gray-500 uppercase">STARTED:</span>
        <span class="text-gray-800">{{ $session->start_time->format('h:i A') }}</span>
      </div>

      <div class="flex justify-between items-center">
        <span class="text-gray-500 uppercase">ENDED:</span>
        <span class="text-gray-800">{{ $session->end_time ? $session->end_time->format('h:i A') : 'Completed' }}</span>
      </div>

      <div class="flex justify-between items-center">
        <span class="text-gray-500 uppercase">TOTAL DURATION:</span>
        <span class="font-bold text-black">{{ $session->formattedDuration() }}</span>
      </div>

      @if($session->isTimeBased())
        <div class="flex justify-between items-center">
          <span class="text-gray-500 uppercase">BILLED MINUTES:</span>
          <span class="font-bold text-black">{{ $session->elapsedMinutes() }} min</span>
        </div>

        <div class="flex justify-between items-center">
          <span class="text-gray-500 uppercase">RATE:</span>
          <span class="text-gray-800">{{ $currency }} {{ number_format($session->rate_applied ?: 10, 0) }} / min</span>
        </div>
      @else
        <div class="flex justify-between items-center">
          <span class="text-gray-500 uppercase">FRAMES PLAYED:</span>
          <span class="font-bold text-black">{{ $session->rounds }} frame(s)</span>
        </div>

        <div class="flex justify-between items-center">
          <span class="text-gray-500 uppercase">RATE / FRAME:</span>
          <span class="text-gray-800">{{ $currency }} {{ number_format($session->rate_applied ?: $session->price_per_round, 0) }}</span>
        </div>
      @endif
    </div>

    <!-- Total & Payment Status -->
    <div class="space-y-3 pt-1">
      <div class="flex justify-between items-baseline border-b-2 border-black pb-2">
        <span class="font-sans font-black text-base uppercase text-gray-950">TOTAL AMOUNT:</span>
        <span class="font-black text-2xl text-black">
          {{ $currency }} {{ number_format($session->total_price, 0) }}
        </span>
      </div>

      <div class="flex justify-between items-center text-xs">
        <span class="text-gray-500 uppercase font-sans font-bold">PAYMENT STATUS:</span>
        <span class="badge-brutal px-3 py-1 font-sans text-xs {{ $session->isPaid() ? 'bg-green-100 text-green-950 border-green-700' : 'bg-red-100 text-red-950 border-red-700' }}">
          {{ strtoupper($session->payment_status) }}
        </span>
      </div>

      @if($session->isPaid())
        <div class="flex justify-between items-center text-xs">
          <span class="text-gray-500 uppercase font-sans font-bold">PAYMENT METHOD:</span>
          <span class="text-gray-900 font-bold uppercase">CASH ONLY</span>
        </div>
        @if($session->payment_time)
          <div class="flex justify-between items-center text-xs">
            <span class="text-gray-500 uppercase font-sans font-bold">PAYMENT TIME:</span>
            <span class="text-gray-900 font-bold">{{ $session->payment_time->format('h:i A') }}</span>
          </div>
        @endif
      @endif

      @if($session->notes)
        <div class="pt-2 text-[11px] text-gray-600 font-sans border-t border-gray-200">
          <span class="font-bold uppercase">Notes:</span> {{ $session->notes }}
        </div>
      @endif
    </div>

    <!-- Thank you footer -->
    <div class="text-center pt-4 border-t-2 border-dashed border-gray-400 text-[11px] text-gray-600 font-sans">
      <p class="font-bold uppercase">Thank you for visiting!</p>
      <p class="text-[10px] text-gray-500">See you again soon 🎱</p>
    </div>

  </div>

  <!-- Bottom Nav Buttons (Hidden on Print) -->
  <div class="flex justify-between items-center no-print pt-2">
    <a href="{{ route('dashboard') }}" class="btn-brutal px-5 py-2.5 bg-white hover:bg-gray-100 text-xs uppercase font-bold">
      Back to Dashboard
    </a>
    <a href="{{ route('sessions.create') }}" class="btn-brutal px-6 py-2.5 bg-brand-yellow hover:bg-yellow-400 text-black text-xs uppercase font-black">
      + New Game Session
    </a>
  </div>

</div>

@push('styles')
<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #receipt-card, #receipt-card * {
      visibility: visible;
    }
    #receipt-card {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      border: none !important;
      box-shadow: none !important;
      padding: 10px !important;
    }
    .no-print {
      display: none !important;
    }
  }
</style>
@endpush
@endsection
