@extends('layouts.app')

@section('title', 'Active Session — ' . ($session->table->name ?? 'Table'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <!-- TOP STATUS BAR: Table & Autosave Indicator -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b-2 border-black">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 bg-brand-yellow border-2 border-black flex items-center justify-center font-black text-lg shadow-brutal-sm">
        @if($session->game_type === 'century') ⏱️
        @elseif($session->game_type === '6_ball') 🎱
        @elseif($session->game_type === '10_ball') 🔴
        @else 🟡
        @endif
      </div>
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-gray-950">
            {{ $session->table->name ?? 'Table' }}
          </h1>
          <span class="badge-brutal px-2.5 py-0.5 bg-black text-brand-yellow text-xs animate-pulse">
            IN PLAY
          </span>
          <span class="badge-brutal px-2 py-0.5 bg-brand-yellow text-black text-xs font-black">
            {{ $session->gameTitle() }}
          </span>
          <span class="font-mono text-xs font-bold text-gray-500">
            #{{ $session->session_code }}
          </span>
        </div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-0.5">
          {{ $session->table->type ?? 'Standard Snooker' }} • Started at {{ $session->start_time->format('h:i A') }} (PKT)
        </p>
      </div>
    </div>

    <!-- Autosave Status Badge -->
    <div class="flex items-center gap-2 self-start sm:self-auto">
      <div id="autosave-indicator" class="badge-brutal px-3 py-1 bg-green-100 text-green-950 border-green-700 text-xs flex items-center gap-1.5 transition">
        <i class="fa-solid fa-check text-green-700" id="autosave-icon"></i>
        <span id="autosave-text">Saved</span>
      </div>
      <a href="{{ route('dashboard') }}" class="btn-brutal px-3 py-1 bg-white hover:bg-gray-100 text-xs uppercase font-bold" title="Back to dashboard without stopping session">
        Dashboard
      </a>
    </div>
  </div>

  <!-- PERSISTENT TABLE LOCK GUARANTEE BANNER -->
  <div class="card-brutal p-3 bg-emerald-50 border-2 border-emerald-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-brutal-sm">
    <div class="flex items-center gap-2.5">
      <span class="w-2.5 h-2.5 bg-emerald-600 rounded-full inline-block animate-ping shrink-0"></span>
      <div class="text-xs text-emerald-950">
        <span class="font-black uppercase tracking-wider text-emerald-900">🟢 TABLE STATUS: LOCKED & PLAYING</span>
        <span class="text-[11px] text-emerald-800 ml-1.5 font-bold">Match will NEVER stop automatically. Table stays strictly booked until you tap "Finish Match".</span>
      </div>
    </div>
    <a href="{{ route('dashboard') }}" class="btn-brutal px-3 py-1 bg-white hover:bg-emerald-100 text-gray-950 text-xs font-black uppercase tracking-wider flex items-center justify-center gap-1.5 shrink-0 border-2 border-black">
      <i class="fa-solid fa-arrow-left text-[10px]"></i>
      <span>Dashboard (Keep Booked)</span>
    </a>
  </div>

  <!-- MAIN OPERATING CONSOLE (NEO-BRUTALIST POS WORKSPACE) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- LEFT COLUMN: LIVE SESSION CONTROLS (8 Cols) -->
    <div class="lg:col-span-8 space-y-6">

      <!-- 1. LIVE PLAYING TIMER BLOCK -->
      <div class="card-brutal p-6 bg-white border-2 border-black space-y-2">
        <div class="flex items-center justify-between text-xs font-black uppercase tracking-wider text-gray-500">
          <span class="flex items-center gap-1.5">
            <i class="fa-solid fa-stopwatch text-black text-sm"></i>
            <span>LIVE PLAYING TIME</span>
          </span>
          <span class="font-mono text-[11px] text-gray-400">Start: {{ $session->start_time->format('h:i:s A') }}</span>
        </div>

        <div class="py-2 text-center sm:text-left">
          <div id="live-timer-display" class="font-mono font-black text-4xl sm:text-6xl text-gray-950 tracking-tight">
            {{ $session->timerClockString() }}
          </div>
          <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">
            Elapsed Hours : Minutes : Seconds
          </p>
        </div>
      </div>

      <!-- 2. GAMEPLAY BILLING CONTROLS -->
      @if($session->isTimeBased())
        <!-- CENTURY (TIME-BASED Rs. 10/MIN) -->
        <div class="card-brutal p-6 bg-yellow-50 border-2 border-black space-y-4">
          <div class="flex items-center justify-between border-b-2 border-black pb-2">
            <div>
              <span class="badge-brutal px-2 py-0.5 bg-black text-brand-yellow text-[10px]">TIME-BASED GAMEPLAY</span>
              <h2 class="text-base font-black uppercase tracking-tight text-gray-950 mt-1">Century Mode Billing</h2>
              <p class="text-xs font-bold text-gray-600">Rate: {{ $currency }} {{ number_format($session->rate_applied ?: 10, 0) }} per minute</p>
            </div>
            <div class="text-right">
              <span class="text-[10px] uppercase font-bold text-gray-500 block">Rate / Min:</span>
              <span class="font-mono font-black text-base text-gray-950">{{ $currency }} {{ number_format($session->rate_applied ?: 10, 0) }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-1">
            <div class="bg-white p-4 border-2 border-black">
              <span class="text-[10px] uppercase font-bold text-gray-500 block">Billed Minutes:</span>
              <span id="century-minutes-display" class="font-mono font-black text-3xl text-gray-950 block mt-0.5">
                {{ $session->elapsedMinutes() }} min
              </span>
              <span class="text-[10px] text-gray-500 font-bold block mt-1">Ceil(seconds / 60)</span>
            </div>

            <div class="bg-white p-4 border-2 border-black">
              <span class="text-[10px] uppercase font-bold text-gray-500 block">Live Amount:</span>
              <span id="century-amount-display" class="font-mono font-black text-3xl text-brand-yellow bg-black px-2 py-0.5 inline-block mt-0.5">
                {{ $currency }} {{ number_format($session->calculateTotal(), 0) }}
              </span>
              <span class="text-[10px] text-gray-500 font-bold block mt-1">Updated in real-time</span>
            </div>
          </div>

          <div class="p-3 bg-white border border-black text-xs font-bold text-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-blue-600"></i>
            <span>Century mode automatically charges per elapsed minute. No manual round entry needed.</span>
          </div>
        </div>

      @else
        <!-- FRAME-BASED (6 BALL, 10 BALL, ONE BALL) -->
        <div class="card-brutal p-6 bg-white space-y-4">
          <div class="flex items-center justify-between border-b-2 border-black pb-2">
            <div>
              <span class="badge-brutal px-2 py-0.5 bg-gray-200 text-gray-900 text-[10px]">FRAME-BASED GAMEPLAY</span>
              <h2 class="text-sm font-black uppercase tracking-wider text-gray-950 mt-1">{{ $session->gameTitle() }} Frames Played</h2>
              <p class="text-xs font-bold text-gray-500">Shopify-style quantity control with real-time recalculation</p>
            </div>
            <div class="text-right">
              <span class="text-[10px] uppercase font-bold text-gray-500 block">Rate / Frame:</span>
              <span class="font-mono font-black text-sm text-gray-950">{{ $currency }} {{ number_format($session->rate_applied ?: $session->price_per_round, 0) }}</span>
            </div>
          </div>

          <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Big Neo-Brutalist Quantity Selector -->
            <div class="flex items-center border-3 border-black shadow-brutal bg-white select-none">
              <!-- Minus button -->
              <button 
                type="button" 
                id="btn-minus-round" 
                onclick="adjustRounds(-1)" 
                class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 border-r-2 border-black text-2xl font-black flex items-center justify-center transition disabled:opacity-40 disabled:cursor-not-allowed"
                {{ $session->rounds <= 1 ? 'disabled' : '' }}
                title="Decrease frame"
              >
                −
              </button>

              <!-- Quantity display -->
              <div class="w-20 sm:w-24 text-center">
                <span id="rounds-display" class="font-mono font-black text-3xl sm:text-4xl text-gray-950 block">
                  {{ $session->rounds }}
                </span>
                <span class="text-[9px] uppercase font-bold tracking-widest text-gray-400 block -mt-1">
                  FRAMES
                </span>
              </div>

              <!-- Plus button -->
              <button 
                type="button" 
                id="btn-plus-round" 
                onclick="adjustRounds(1)" 
                class="w-14 h-14 sm:w-16 sm:h-16 bg-brand-yellow hover:bg-yellow-400 active:bg-yellow-500 border-l-2 border-black text-2xl font-black flex items-center justify-center transition"
                title="Add frame"
              >
                +
              </button>
            </div>

            <!-- Formula explainer -->
            <div class="text-center sm:text-right bg-gray-50 p-3 border-2 border-black w-full sm:w-auto flex-1">
              <p class="text-xs font-bold text-gray-600 uppercase">Calculation Formula</p>
              <p class="font-mono font-bold text-sm text-gray-950 mt-0.5">
                <span id="formula-rounds">{{ $session->rounds }}</span> frames × {{ $currency }}{{ number_format($session->rate_applied ?: $session->price_per_round, 0) }}
              </p>
              <p class="text-xs font-black text-gray-950 uppercase mt-1">
                = <span id="formula-total" class="text-base text-black font-mono font-black">{{ $currency }} {{ number_format($session->total_price, 0) }}</span>
              </p>
            </div>
          </div>
        </div>
      @endif

      <!-- 3. CUSTOMER & SESSION DETAILS FORM (AUTOSAVED) -->
      <div class="card-brutal p-6 bg-white space-y-4">
        <h2 class="text-xs font-black uppercase tracking-wider text-gray-950 border-b-2 border-black pb-2 flex items-center justify-between">
          <span>Customer & Notes</span>
          <span class="text-[10px] text-gray-500 lowercase font-normal">changes autosave</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1">Customer Name</label>
            <input 
              type="text" 
              id="cust-name-input" 
              value="{{ $session->customer->name ?? '' }}" 
              class="w-full input-brutal px-3.5 py-2 text-sm font-bold text-gray-950"
              onchange="triggerAutosave()"
            >
          </div>
          <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1">Phone Number (Optional)</label>
            <input 
              type="tel" 
              id="cust-phone-input" 
              value="{{ $session->customer->phone ?? '' }}" 
              class="w-full input-brutal px-3.5 py-2 text-sm font-mono text-gray-950"
              placeholder="e.g. 0300-1234567"
              onchange="triggerAutosave()"
            >
          </div>
        </div>

        <div>
          <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1">Session Notes / Match Info</label>
          <input 
            type="text" 
            id="session-notes-input" 
            value="{{ $session->notes }}" 
            placeholder="e.g. VIP cue sticks, referee notes, etc."
            class="w-full input-brutal px-3.5 py-2 text-xs text-gray-950"
            onchange="triggerAutosave()"
          >
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: BILLING, CASH PAYMENT & CHECKOUT (4 Cols) -->
    <div class="lg:col-span-4 space-y-6">

      <!-- BILL SUMMARY & CASH PAYMENT CARD -->
      <div class="card-brutal p-6 bg-white space-y-5 border-2 border-black shadow-brutal-lg">
        <h2 class="text-sm font-black uppercase tracking-wider text-gray-950 border-b-2 border-black pb-2 flex items-center justify-between">
          <span>Billing Summary</span>
          <span class="badge-brutal px-1.5 py-0.2 bg-black text-brand-yellow text-[9px]">CASH ONLY</span>
        </h2>

        <!-- Breakdown List -->
        <div class="space-y-2.5 text-xs">
          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Customer:</span>
            <span class="font-black text-gray-950 text-sm text-right" id="summary-cust-name">{{ $session->customer->name ?? 'Guest' }}</span>
          </div>

          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Table:</span>
            <span class="font-black text-gray-950 text-sm">{{ $session->table->name ?? 'Table' }}</span>
          </div>

          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Gameplay:</span>
            <span class="font-black text-gray-950 text-sm">{{ $session->gameTitle() }}</span>
          </div>

          @if($session->isTimeBased())
            <div class="flex justify-between items-center text-gray-600 font-bold">
              <span class="uppercase">Rate:</span>
              <span class="font-mono font-black text-gray-950 text-sm">{{ $currency }} {{ number_format($session->rate_applied ?: 10, 0) }} / min</span>
            </div>
            <div class="flex justify-between items-center text-gray-600 font-bold">
              <span class="uppercase">Billed Time:</span>
              <span class="font-mono font-black text-gray-950 text-sm" id="summary-time-minutes">{{ $session->elapsedMinutes() }} min</span>
            </div>
          @else
            <div class="flex justify-between items-center text-gray-600 font-bold">
              <span class="uppercase">Frames Played:</span>
              <span class="font-mono font-black text-gray-950 text-sm" id="summary-rounds">{{ $session->rounds }}</span>
            </div>
            <div class="flex justify-between items-center text-gray-600 font-bold">
              <span class="uppercase">Rate / Frame:</span>
              <span class="font-mono font-black text-gray-950 text-sm">{{ $currency }} {{ number_format($session->rate_applied ?: $session->price_per_round, 0) }}</span>
            </div>
          @endif

          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Duration:</span>
            <span class="font-mono font-bold text-gray-950" id="summary-duration">--</span>
          </div>

          <!-- Grand Total Box -->
          <div class="pt-3 border-t-2 border-black bg-yellow-50 -mx-6 px-6 py-4 border-b-2">
            <div class="flex justify-between items-baseline">
              <span class="text-xs font-black uppercase tracking-wider text-gray-950">TOTAL BILL:</span>
              <span id="summary-grand-total" class="font-mono font-black text-2xl sm:text-3xl text-black">
                {{ $currency }} {{ number_format($session->calculateTotal(), 0) }}
              </span>
            </div>
          </div>
        </div>

        <!-- PAYMENT STATUS TOGGLE: CASH ONLY -->
        <div class="space-y-2 pt-2">
          <label class="block text-xs font-black uppercase tracking-wider text-gray-950 flex justify-between">
            <span>Cash Payment</span>
            <span id="payment-time-label" class="text-[10px] font-mono text-gray-500 font-bold">
              {{ $session->isPaid() && $session->payment_time ? 'Paid at ' . $session->payment_time->format('h:i A') : '' }}
            </span>
          </label>

          <!-- Big Neo-Brutalist Toggle Buttons -->
          <div class="grid grid-cols-2 gap-2">
            <button 
              type="button" 
              id="btn-status-unpaid" 
              onclick="setPaymentStatus('unpaid')" 
              class="btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 {{ $session->isUnpaid() ? 'bg-red-500 text-white border-black shadow-brutal-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
              <i class="fa-solid fa-clock"></i>
              <span>UNPAID</span>
            </button>

            <button 
              type="button" 
              id="btn-status-paid" 
              onclick="setPaymentStatus('paid')" 
              class="btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 {{ $session->isPaid() ? 'bg-green-600 text-white border-black shadow-brutal-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
              <i class="fa-solid fa-money-bill-wave"></i>
              <span>PAID (CASH)</span>
            </button>
          </div>
        </div>

        <!-- ACTIONS & FINISH MATCH CONTROLS -->
        <div class="pt-3 border-t-2 border-black space-y-3">
          <!-- Information message on autosaving -->
          <div class="p-2.5 bg-blue-50 border border-blue-500 text-blue-950 text-[11px] font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-blue-600 shrink-0"></i>
            <span>All entries autosave continuously. Session remains active in the background.</span>
          </div>

          <!-- Safe Navigation: Return to Dashboard while leaving table booked -->
          <a href="{{ route('dashboard') }}" class="w-full btn-brutal py-3 bg-white hover:bg-gray-100 text-gray-950 text-xs font-black uppercase tracking-wider flex items-center justify-center gap-2 border-2 border-black shadow-brutal-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Return to Dashboard (Keep Table Booked)</span>
          </a>

          <!-- Primary Finish Match Button: Requires explicit modal confirmation -->
          <button 
            type="button" 
            onclick="openEndMatchModal()" 
            id="btn-open-finish-modal"
            class="w-full btn-brutal py-4 bg-brand-yellow hover:bg-yellow-400 text-black text-sm font-black uppercase tracking-wider flex items-center justify-center gap-2 shadow-brutal border-2 border-black"
          >
            <i class="fa-solid fa-flag-checkered text-base"></i>
            <span>FINISH MATCH & FREE TABLE</span>
          </button>

          <!-- Safe Discard / Cancel Option -->
          <div class="text-center pt-1">
            <button 
              type="button" 
              onclick="openCancelModal()" 
              class="text-xs font-bold text-red-600 hover:text-red-800 underline transition"
            >
              Cancel Match without Billing
            </button>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- 1. END MATCH CONFIRMATION MODAL (NEO-BRUTALIST POS CONFIRMATION) -->
  <div id="end-match-modal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="card-brutal max-w-lg w-full bg-white p-6 border-3 border-black shadow-brutal-lg space-y-4 animate-scale-up">
      <div class="flex items-center justify-between border-b-2 border-black pb-2">
        <div class="flex items-center gap-2">
          <span class="text-2xl">🏁</span>
          <h3 class="text-base sm:text-lg font-black uppercase text-gray-950">Finish Match & Settle Bill</h3>
        </div>
        <button type="button" onclick="closeEndMatchModal()" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 border-2 border-black font-black text-sm flex items-center justify-center">✕</button>
      </div>

      <div class="p-3 bg-amber-50 border-2 border-amber-600 text-amber-950 text-xs font-bold space-y-1">
        <p class="font-black uppercase flex items-center gap-1.5">
          <i class="fa-solid fa-triangle-exclamation text-amber-700"></i>
          <span>Are the players finished on this table?</span>
        </p>
        <p>Once you confirm, the match timer will stop, the final bill will be logged, and <strong>{{ $session->table->name ?? 'the table' }}</strong> will be marked <strong>AVAILABLE</strong> for new customers.</p>
      </div>

      <!-- Live summary snapshot inside modal -->
      <div class="bg-gray-50 border-2 border-black p-4 space-y-2 text-xs">
        <div class="flex justify-between items-center text-gray-600 font-bold">
          <span class="uppercase">Customer:</span>
          <span class="font-black text-gray-950 text-sm" id="modal-cust-name">{{ $session->customer->name ?? 'Guest' }}</span>
        </div>
        <div class="flex justify-between items-center text-gray-600 font-bold">
          <span class="uppercase">Table & Game:</span>
          <span class="font-black text-gray-950">{{ $session->table->name ?? 'Table' }} ({{ $session->gameTitle() }})</span>
        </div>
        <div class="flex justify-between items-center text-gray-600 font-bold">
          <span class="uppercase">Total Playing Time:</span>
          <span class="font-mono font-bold text-gray-950" id="modal-duration">--</span>
        </div>
        <div class="flex justify-between items-center text-gray-600 font-bold">
          <span class="uppercase">Total Amount:</span>
          <span class="font-mono font-black text-base text-gray-950" id="modal-total-price">Rs. --</span>
        </div>
        <div class="flex justify-between items-center text-gray-600 font-bold">
          <span class="uppercase">Payment Status:</span>
          <span class="font-mono font-black text-xs px-2.5 py-0.5" id="modal-payment-badge">UNPAID</span>
        </div>
      </div>

      <!-- Two-step submit form with confirm_checkout verification -->
      <form action="{{ route('sessions.checkout', $session->id, false) }}" method="POST" id="modal-checkout-form">
        @csrf
        <input type="hidden" name="confirm_checkout" value="yes">
        <input type="hidden" name="payment_status" id="modal-input-payment-status" value="{{ $session->payment_status }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
          <button type="button" onclick="closeEndMatchModal()" class="btn-brutal py-3 bg-white hover:bg-gray-100 text-black text-xs font-black uppercase tracking-wider">
            ✕ No, Keep Playing
          </button>
          <button type="submit" class="btn-brutal py-3 bg-brand-yellow hover:bg-yellow-400 text-black text-xs font-black uppercase tracking-wider shadow-brutal flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-check"></i>
            <span>YES, FINISH & FREE TABLE</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- 2. CANCEL MATCH MODAL -->
  <div id="cancel-match-modal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="card-brutal max-w-md w-full bg-white p-6 border-3 border-black shadow-brutal-lg space-y-4">
      <div class="flex items-center justify-between border-b-2 border-black pb-2">
        <h3 class="text-base font-black uppercase text-red-600 flex items-center gap-2">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span>Cancel Match Session</span>
        </h3>
        <button type="button" onclick="closeCancelModal()" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 border-2 border-black font-black text-sm flex items-center justify-center">✕</button>
      </div>
      <p class="text-xs font-bold text-gray-700">
        Are you sure you want to cancel this session? No bill will be recorded, and <strong>{{ $session->table->name ?? 'this table' }}</strong> will immediately become available.
      </p>
      <form action="{{ route('sessions.cancel', $session->id, false) }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-3 pt-2">
          <button type="button" onclick="closeCancelModal()" class="btn-brutal py-2.5 bg-white hover:bg-gray-100 text-black text-xs font-black uppercase">
            Keep Playing
          </button>
          <button type="submit" class="btn-brutal py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase shadow-brutal">
            Yes, Cancel Match
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

@push('scripts')
<script>
  // Session Configuration from Server
  const SESSION_ID = {{ $session->id }};
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const START_TIMESTAMP = new Date("{{ $session->start_time->toISOString() }}").getTime();
  const IS_TIME_BASED = {{ $session->isTimeBased() ? 'true' : 'false' }};
  const RATE_APPLIED = {{ (float) ($session->rate_applied ?: $session->price_per_round) }};
  const CURRENCY = "{{ $currency }}";

  let currentRounds = {{ $session->rounds }};
  let currentPaymentStatus = "{{ $session->payment_status }}";

  // ==========================================
  // 1. LIVE PLAYING TIMER & CENTURY PRICE
  // ==========================================
  function updateLiveTimer() {
    const now = new Date().getTime();
    const diffSec = Math.max(0, Math.floor((now - START_TIMESTAMP) / 1000));

    const hrs = Math.floor(diffSec / 3600);
    const mins = Math.floor((diffSec % 3600) / 60);
    const secs = diffSec % 60;

    const clockStr = String(hrs).padStart(2, '0') + ':' + 
                     String(mins).padStart(2, '0') + ':' + 
                     String(secs).padStart(2, '0');

    const displayEl = document.getElementById('live-timer-display');
    if (displayEl) displayEl.innerText = clockStr;

    const summaryEl = document.getElementById('summary-duration');
    if (summaryEl) summaryEl.innerText = hrs > 0 ? `${hrs}h ${mins}m ${secs}s` : `${mins}m ${secs}s`;

    // If Century (Time-Based), dynamically compute and tick live price
    if (IS_TIME_BASED) {
      const elapsedMins = Math.max(1, Math.ceil(diffSec / 60));
      const liveTotal = elapsedMins * RATE_APPLIED;
      const formattedTotal = CURRENCY + ' ' + liveTotal.toLocaleString();

      const minsEl = document.getElementById('century-minutes-display');
      if (minsEl) minsEl.innerText = elapsedMins + ' min';

      const sumMinsEl = document.getElementById('summary-time-minutes');
      if (sumMinsEl) sumMinsEl.innerText = elapsedMins + ' min';

      const amtEl = document.getElementById('century-amount-display');
      if (amtEl) amtEl.innerText = formattedTotal;

      const grandTotalEl = document.getElementById('summary-grand-total');
      if (grandTotalEl) grandTotalEl.innerText = formattedTotal;
    }
  }

  setInterval(updateLiveTimer, 1000);
  updateLiveTimer();

  // Periodic background sync with server every 15s to keep perfectly in sync
  setInterval(async () => {
    try {
      const res = await fetch(`/sessions/${SESSION_ID}/live-status`);
      const data = await res.json();
      if (data && data.status === 'active') {
        if (!IS_TIME_BASED) {
          currentRounds = data.rounds;
          const roundsDisplay = document.getElementById('rounds-display');
          if (roundsDisplay) roundsDisplay.innerText = currentRounds;
        }
        document.getElementById('summary-grand-total').innerText = data.formatted_total;
      }
    } catch(e) {}
  }, 15000);

  // ==========================================
  // 2. ROUND SYSTEM (FOR FRAME-BASED GAMES)
  // ==========================================
  async function adjustRounds(delta) {
    if (IS_TIME_BASED) return;
    const newQty = currentRounds + delta;
    if (newQty < 1) return;

    setAutosaveState('saving');

    try {
      const res = await fetch(`/sessions/${SESSION_ID}/rounds`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ rounds: newQty })
      });

      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Error updating rounds');

      currentRounds = data.rounds;
      document.getElementById('rounds-display').innerText = currentRounds;
      document.getElementById('formula-rounds').innerText = currentRounds;
      document.getElementById('summary-rounds').innerText = currentRounds;
      document.getElementById('formula-total').innerText = data.formatted_total;
      document.getElementById('summary-grand-total').innerText = data.formatted_total;

      const btnMinus = document.getElementById('btn-minus-round');
      if (btnMinus) btnMinus.disabled = (currentRounds <= 1);

      setAutosaveState('saved', data.saved_at);
    } catch (err) {
      console.error(err);
      setAutosaveState('error');
      alert('Unable to update rounds: ' + err.message);
    }
  }

  // ==========================================
  // 3. PAYMENT STATUS TOGGLE (CASH ONLY)
  // ==========================================
  async function setPaymentStatus(status) {
    setAutosaveState('saving');

    try {
      const res = await fetch(`/sessions/${SESSION_ID}/payment`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ payment_status: status })
      });

      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Error updating payment');

      currentPaymentStatus = status;
      document.getElementById('form-payment-status').value = status;

      const btnUnpaid = document.getElementById('btn-status-unpaid');
      const btnPaid = document.getElementById('btn-status-paid');
      const timeLabel = document.getElementById('payment-time-label');

      if (status === 'paid') {
        btnPaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-green-600 text-white border-black shadow-brutal-sm';
        btnUnpaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200';
        timeLabel.innerText = data.payment_time ? `Paid at ${data.payment_time}` : 'Paid';
      } else {
        btnUnpaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-red-500 text-white border-black shadow-brutal-sm';
        btnPaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200';
        timeLabel.innerText = '';
      }

      setAutosaveState('saved', data.saved_at);
    } catch (err) {
      console.error(err);
      setAutosaveState('error');
      alert('Unable to update payment status: ' + err.message);
    }
  }

  // ==========================================
  // 4. AUTOSAVE SYSTEM
  // ==========================================
  let autosaveTimeout = null;

  function triggerAutosave() {
    clearTimeout(autosaveTimeout);
    setAutosaveState('saving');

    autosaveTimeout = setTimeout(async () => {
      const custName = document.getElementById('cust-name-input').value.trim();
      const custPhone = document.getElementById('cust-phone-input').value.trim();
      const notes = document.getElementById('session-notes-input').value.trim();

      document.getElementById('summary-cust-name').innerText = custName || 'Guest';

      try {
        const res = await fetch(`/sessions/${SESSION_ID}/autosave`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            customer_name: custName,
            customer_phone: custPhone,
            notes: notes
          })
        });

        const data = await res.json();
        if (!data.success) throw new Error('Autosave failed');

        setAutosaveState('saved', data.saved_at);
      } catch (err) {
        console.error(err);
        setAutosaveState('error');
      }
    }, 600);
  }

  function setAutosaveState(state, timeStr = '') {
    const badge = document.getElementById('autosave-indicator');
    const icon = document.getElementById('autosave-icon');
    const text = document.getElementById('autosave-text');

    if (state === 'saving') {
      badge.className = 'badge-brutal px-3 py-1 bg-yellow-100 text-yellow-950 border-black text-xs flex items-center gap-1.5';
      icon.className = 'fa-solid fa-spinner fa-spin text-yellow-900';
      text.innerText = 'Saving...';
    } else if (state === 'saved') {
      badge.className = 'badge-brutal px-3 py-1 bg-green-100 text-green-950 border-green-700 text-xs flex items-center gap-1.5';
      icon.className = 'fa-solid fa-check text-green-700';
      text.innerText = timeStr ? `Saved ${timeStr}` : 'Saved';
    } else if (state === 'error') {
      badge.className = 'badge-brutal px-3 py-1 bg-red-100 text-red-950 border-red-700 text-xs flex items-center gap-1.5';
      icon.className = 'fa-solid fa-triangle-exclamation text-red-700';
      text.innerText = '⚠ Unable to save';
    }
  }

  // ==========================================
  // 5. MODAL MANAGEMENT (END MATCH & CANCEL)
  // ==========================================
  function openEndMatchModal() {
    const total = document.getElementById('summary-grand-total').innerText;
    const duration = document.getElementById('summary-duration').innerText;
    const custName = document.getElementById('summary-cust-name').innerText;

    document.getElementById('modal-cust-name').innerText = custName;
    document.getElementById('modal-duration').innerText = duration;
    document.getElementById('modal-total-price').innerText = total;

    const badge = document.getElementById('modal-payment-badge');
    const modalInput = document.getElementById('modal-input-payment-status');
    if (modalInput) modalInput.value = currentPaymentStatus;

    if (badge) {
      if (currentPaymentStatus === 'paid') {
        badge.className = 'font-mono font-black text-xs px-2.5 py-0.5 bg-green-600 text-white';
        badge.innerText = 'PAID (CASH)';
      } else {
        badge.className = 'font-mono font-black text-xs px-2.5 py-0.5 bg-red-600 text-white';
        badge.innerText = 'UNPAID';
      }
    }

    document.getElementById('end-match-modal').classList.remove('hidden');
  }

  function closeEndMatchModal() {
    document.getElementById('end-match-modal').classList.add('hidden');
  }

  function openCancelModal() {
    document.getElementById('cancel-match-modal').classList.remove('hidden');
  }

  function closeCancelModal() {
    document.getElementById('cancel-match-modal').classList.add('hidden');
  }

  // Close modals on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeEndMatchModal();
      closeCancelModal();
    }
  });
</script>
@endpush
@endsection
