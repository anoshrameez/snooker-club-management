@extends('layouts.app')

@section('title', 'Active Session — ' . ($session->table->name ?? 'Table'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <!-- TOP STATUS BAR: Table & Autosave Indicator -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b-2 border-black">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 bg-brand-yellow border-2 border-black flex items-center justify-center font-black text-lg shadow-brutal-sm">
        🎱
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl sm:text-2xl font-black uppercase tracking-tight text-gray-950">
            {{ $session->table->name ?? 'Table' }}
          </h1>
          <span class="badge-brutal px-2.5 py-0.5 bg-black text-brand-yellow text-xs animate-pulse">
            IN PLAY
          </span>
          <span class="font-mono text-xs font-bold text-gray-500">
            #{{ $session->session_code }}
          </span>
        </div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-0.5">
          {{ $session->table->type ?? 'Standard Snooker' }} • Started at {{ $session->start_time->format('h:i A') }}
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
          <span class="font-mono text-[11px] text-gray-400">Server Start: {{ $session->start_time->format('h:i:s A') }}</span>
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

      <!-- 2. CUSTOMER & SESSION DETAILS FORM (AUTOSAVED) -->
      <div class="card-brutal p-6 bg-white space-y-4">
        <h2 class="text-xs font-black uppercase tracking-wider text-gray-950 border-b-2 border-black pb-2 flex items-center justify-between">
          <span>Customer & Session Details</span>
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
            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1">Phone Number</label>
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

      <!-- 3. ROUND SYSTEM (SHOPIFY QUANTITY STYLE) -->
      <div class="card-brutal p-6 bg-white space-y-4">
        <div class="flex items-center justify-between border-b-2 border-black pb-2">
          <div>
            <h2 class="text-sm font-black uppercase tracking-wider text-gray-950">Rounds / Frames Played</h2>
            <p class="text-xs font-bold text-gray-500">Shopify-style quantity control with real-time recalculation</p>
          </div>
          <div class="text-right">
            <span class="text-[10px] uppercase font-bold text-gray-500 block">Rate / Frame:</span>
            <span class="font-mono font-black text-sm text-gray-950">{{ $currency }} {{ number_format($session->price_per_round, 0) }}</span>
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
              title="Decrease round"
            >
              −
            </button>

            <!-- Quantity display / manual edit -->
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
              title="Add round"
            >
              +
            </button>
          </div>

          <!-- Formula explainer -->
          <div class="text-center sm:text-right bg-gray-50 p-3 border-2 border-black w-full sm:w-auto flex-1">
            <p class="text-xs font-bold text-gray-600 uppercase">Calculation Formula</p>
            <p class="font-mono font-bold text-sm text-gray-950 mt-0.5">
              <span id="formula-rounds">{{ $session->rounds }}</span> frames × {{ $currency }}{{ number_format($session->price_per_round, 0) }}
            </p>
            <p class="text-xs font-black text-gray-950 uppercase mt-1">
              = <span id="formula-total" class="text-base text-black font-mono font-black">{{ $currency }} {{ number_format($session->total_price, 0) }}</span>
            </p>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: BILLING, PAYMENT & CHECKOUT (4 Cols) -->
    <div class="lg:col-span-4 space-y-6">

      <!-- BILL SUMMARY & PAYMENT CARD -->
      <div class="card-brutal p-6 bg-white space-y-5 border-2 border-black shadow-brutal-lg">
        <h2 class="text-sm font-black uppercase tracking-wider text-gray-950 border-b-2 border-black pb-2 flex items-center justify-between">
          <span>Billing Summary</span>
          <span class="font-mono text-xs text-gray-500">LIVE</span>
        </h2>

        <!-- Breakdown List -->
        <div class="space-y-2.5 text-xs">
          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Customer:</span>
            <span class="font-black text-gray-950 text-sm text-right" id="summary-cust-name">{{ $session->customer->name ?? 'Guest' }}</span>
          </div>

          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Table Assigned:</span>
            <span class="font-black text-gray-950 text-sm">{{ $session->table->name ?? 'Table' }}</span>
          </div>

          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Total Frames:</span>
            <span class="font-mono font-black text-gray-950 text-sm" id="summary-rounds">{{ $session->rounds }}</span>
          </div>

          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Price Per Frame:</span>
            <span class="font-mono font-black text-gray-950 text-sm">{{ $currency }} {{ number_format($session->price_per_round, 0) }}</span>
          </div>

          <div class="flex justify-between items-center text-gray-600 font-bold">
            <span class="uppercase">Live Duration:</span>
            <span class="font-mono font-bold text-gray-950" id="summary-duration">--</span>
          </div>

          <!-- Grand Total Box -->
          <div class="pt-3 border-t-2 border-black bg-yellow-50 -mx-6 px-6 py-4 border-b-2">
            <div class="flex justify-between items-baseline">
              <span class="text-xs font-black uppercase tracking-wider text-gray-950">TOTAL BILL:</span>
              <span id="summary-grand-total" class="font-mono font-black text-2xl sm:text-3xl text-black">
                {{ $currency }} {{ number_format($session->total_price, 0) }}
              </span>
            </div>
          </div>
        </div>

        <!-- PAYMENT STATUS TOGGLE: SECTION 11 -->
        <div class="space-y-2 pt-2">
          <label class="block text-xs font-black uppercase tracking-wider text-gray-950 flex justify-between">
            <span>Payment Status</span>
            <span id="payment-time-label" class="text-[10px] font-mono text-gray-500 font-bold">
              {{ $session->isPaid() && $session->payment_time ? $session->payment_time->format('h:i A') : '' }}
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
              class="btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 {{ $session->isPaid() ? 'bg-green-500 text-white border-black shadow-brutal-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
              <i class="fa-solid fa-check"></i>
              <span>PAID</span>
            </button>
          </div>

          <!-- Payment Method Choice (When Paid) -->
          <div id="payment-method-selector" class="{{ $session->isPaid() ? '' : 'hidden' }} pt-2 space-y-1">
            <label class="block text-[10px] font-black uppercase text-gray-600">Payment Method:</label>
            <select id="payment-method-select" onchange="triggerPaymentMethodUpdate()" class="w-full input-brutal px-2.5 py-1.5 text-xs font-bold text-gray-900">
              <option value="cash" {{ $session->payment_method === 'cash' ? 'selected' : '' }}>Cash Counter</option>
              <option value="online" {{ $session->payment_method === 'online' ? 'selected' : '' }}>Online / UPI / Bank</option>
              <option value="card" {{ $session->payment_method === 'card' ? 'selected' : '' }}>Card Machine</option>
            </select>
          </div>
        </div>

        <!-- CHECKOUT & SAVE: SECTION 12 -->
        <div class="pt-3 border-t-2 border-black space-y-2">
          <form action="{{ route('sessions.checkout', $session->id) }}" method="POST" id="checkout-form" onsubmit="return confirmCheckout()">
            @csrf
            <input type="hidden" name="payment_status" id="form-payment-status" value="{{ $session->payment_status }}">
            <input type="hidden" name="payment_method" id="form-payment-method" value="{{ $session->payment_method ?? 'cash' }}">

            <button 
              type="submit" 
              id="btn-checkout-submit" 
              class="w-full btn-brutal py-4 bg-brand-yellow hover:bg-yellow-400 text-black text-sm font-black uppercase tracking-wider flex items-center justify-center gap-2 shadow-brutal"
            >
              <i class="fa-solid fa-circle-check text-base"></i>
              <span>CHECKOUT & SAVE</span>
            </button>
          </form>

          <!-- Cancel Session Option -->
          <form action="{{ route('sessions.cancel', $session->id) }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to cancel this session? The table will be immediately freed and this game will not be counted in revenue.')">
            @csrf
            <button type="submit" class="w-full btn-brutal py-2 bg-white hover:bg-red-50 text-red-600 text-xs font-bold uppercase transition">
              Cancel Session
            </button>
          </form>
        </div>

      </div>

    </div>

  </div>

</div>

@push('scripts')
<script>
  // Session Configuration from Server
  const SESSION_ID = {{ $session->id }};
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const START_TIMESTAMP = new Date("{{ $session->start_time->toISOString() }}").getTime();
  const PRICE_PER_ROUND = {{ $session->price_per_round }};
  const CURRENCY = "{{ $currency }}";

  let currentRounds = {{ $session->rounds }};
  let currentPaymentStatus = "{{ $session->payment_status }}";
  let currentPaymentMethod = "{{ $session->payment_method ?? 'cash' }}";

  // ==========================================
  // 1. LIVE PLAYING TIMER (SECTION 10)
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
  }

  setInterval(updateLiveTimer, 1000);
  updateLiveTimer();

  // ==========================================
  // 2. ROUND SYSTEM (SECTION 8)
  // ==========================================
  async function adjustRounds(delta) {
    const newQty = currentRounds + delta;
    if (newQty < 1) return; // Never allow below 1 for active session

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

      // Update state & UI
      currentRounds = data.rounds;
      document.getElementById('rounds-display').innerText = currentRounds;
      document.getElementById('formula-rounds').innerText = currentRounds;
      document.getElementById('summary-rounds').innerText = currentRounds;
      document.getElementById('formula-total').innerText = data.formatted_total;
      document.getElementById('summary-grand-total').innerText = data.formatted_total;

      // Update minus button disabled state
      document.getElementById('btn-minus-round').disabled = (currentRounds <= 1);

      setAutosaveState('saved', data.saved_at);
    } catch (err) {
      console.error(err);
      setAutosaveState('error');
      alert('Unable to update rounds: ' + err.message);
    }
  }

  // ==========================================
  // 3. PAYMENT STATUS TOGGLE (SECTION 11)
  // ==========================================
  async function setPaymentStatus(status) {
    setAutosaveState('saving');
    const method = document.getElementById('payment-method-select')?.value || 'cash';

    try {
      const res = await fetch(`/sessions/${SESSION_ID}/payment`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ payment_status: status, payment_method: method })
      });

      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Error updating payment');

      currentPaymentStatus = status;
      document.getElementById('form-payment-status').value = status;

      // Update Button Styles
      const btnUnpaid = document.getElementById('btn-status-unpaid');
      const btnPaid = document.getElementById('btn-status-paid');
      const methodBox = document.getElementById('payment-method-selector');
      const timeLabel = document.getElementById('payment-time-label');

      if (status === 'paid') {
        btnPaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-green-500 text-white border-black shadow-brutal-sm';
        btnUnpaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200';
        methodBox.classList.remove('hidden');
        timeLabel.innerText = data.payment_time ? `Paid at ${data.payment_time}` : '';
      } else {
        btnUnpaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-red-500 text-white border-black shadow-brutal-sm';
        btnPaid.className = 'btn-brutal py-3 text-xs uppercase font-black transition flex items-center justify-center gap-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200';
        methodBox.classList.add('hidden');
        timeLabel.innerText = '';
      }

      setAutosaveState('saved', data.saved_at);
    } catch (err) {
      console.error(err);
      setAutosaveState('error');
      alert('Unable to update payment status: ' + err.message);
    }
  }

  function triggerPaymentMethodUpdate() {
    if (currentPaymentStatus === 'paid') {
      const method = document.getElementById('payment-method-select').value;
      document.getElementById('form-payment-method').value = method;
      setPaymentStatus('paid');
    }
  }

  // ==========================================
  // 4. AUTOSAVE SYSTEM (SECTION 13)
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
  // 5. CHECKOUT CONFIRMATION (SECTION 12)
  // ==========================================
  function confirmCheckout() {
    const total = document.getElementById('summary-grand-total').innerText;
    const payment = currentPaymentStatus.toUpperCase();

    const msg = `Are you sure you want to finish this session?\n\nCustomer: ${document.getElementById('summary-cust-name').innerText}\nRounds: ${currentRounds} frame(s)\nTotal Bill: ${total}\nPayment Status: ${payment}\n\nTable will be marked AVAILABLE.`;
    return confirm(msg);
  }
</script>
@endpush
@endsection
