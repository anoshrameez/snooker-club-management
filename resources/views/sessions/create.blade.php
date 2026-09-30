@extends('layouts.app')

@section('title', 'New Game Session')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <!-- Breadcrumb / Header -->
  <div class="border-b-2 border-black pb-3 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-play text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Start New Game Session</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Assign Table & Gameplay Mode
      </p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn-brutal px-3 py-1.5 bg-white text-xs uppercase font-bold">
      ✕ Cancel
    </a>
  </div>

  @if($availableTables->isEmpty())
    <div class="card-brutal p-8 bg-red-50 border-red-600 border-2 text-center space-y-3">
      <i class="fa-solid fa-triangle-exclamation text-4xl text-red-600"></i>
      <h2 class="text-lg font-black text-red-950 uppercase">No Tables Available Right Now</h2>
      <p class="text-xs font-bold text-red-800 max-w-md mx-auto">
        All tables are currently occupied with active games. Check out an active session on the dashboard to free up a table.
      </p>
      <div class="pt-2">
        <a href="{{ route('dashboard') }}" class="btn-brutal inline-block px-5 py-2.5 bg-black text-white text-xs uppercase font-bold">
          View Active Sessions
        </a>
      </div>
    </div>
  @else

    <!-- Main New Session Form Card -->
    <div class="card-brutal p-6 sm:p-8 bg-white space-y-6">
      <form action="{{ route('sessions.store', [], false) }}" method="POST" id="new-session-form" class="space-y-6">
        @csrf

        <!-- 1. Customer Name (Clean, direct text input - No autocomplete clutter) -->
        <div class="space-y-1.5">
          <label for="customer_name" class="block text-xs font-black uppercase tracking-wider text-gray-950">
            1. Customer / Player Name <span class="text-red-600">*</span>
          </label>
          <input 
            type="text" 
            name="customer_name" 
            id="customer_name" 
            required 
            value="{{ old('customer_name') }}"
            placeholder="Enter customer name..." 
            class="w-full input-brutal px-4 py-3 text-base text-gray-950 font-bold"
            autofocus
          >
        </div>

        <!-- 2. Gameplay Mode Selection (The 4 Official Modes) -->
        <div class="space-y-2">
          <label class="block text-xs font-black uppercase tracking-wider text-gray-950">
            2. Select Gameplay Mode <span class="text-red-600">*</span>
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            
            <!-- Century -->
            <label class="cursor-pointer border-2 border-black p-3.5 block relative select-none gameplay-choice bg-brand-yellow shadow-brutal-sm ring-2 ring-black transition">
              <input 
                type="radio" 
                name="game_type" 
                value="century" 
                class="sr-only" 
                {{ old('game_type', 'century') === 'century' ? 'checked' : '' }}
                onchange="updateGameplaySelectionUI()"
              >
              <div class="flex items-center justify-between mb-1">
                <span class="text-lg">⏱️</span>
                <span class="badge-brutal px-1.5 py-0.5 bg-black text-brand-yellow text-[9px]">TIME</span>
              </div>
              <div class="font-black text-sm uppercase text-gray-950">Century</div>
              <div class="text-[11px] font-bold text-gray-800 mt-1 game-rate-display" data-game="century">
                Rs. 10 / min
              </div>
            </label>

            <!-- 6 Ball -->
            <label class="cursor-pointer border-2 border-black p-3.5 block relative select-none gameplay-choice bg-gray-50 hover:bg-gray-100 transition">
              <input 
                type="radio" 
                name="game_type" 
                value="6_ball" 
                class="sr-only" 
                {{ old('game_type') === '6_ball' ? 'checked' : '' }}
                onchange="updateGameplaySelectionUI()"
              >
              <div class="flex items-center justify-between mb-1">
                <span class="text-lg">🎱</span>
                <span class="badge-brutal px-1.5 py-0.5 bg-gray-200 text-gray-900 text-[9px]">FRAME</span>
              </div>
              <div class="font-black text-sm uppercase text-gray-950">6 Ball</div>
              <div class="text-[11px] font-bold text-gray-800 mt-1 game-rate-display" data-game="6_ball">
                Rs. 130 / frame
              </div>
            </label>

            <!-- 10 Ball -->
            <label class="cursor-pointer border-2 border-black p-3.5 block relative select-none gameplay-choice bg-gray-50 hover:bg-gray-100 transition">
              <input 
                type="radio" 
                name="game_type" 
                value="10_ball" 
                class="sr-only" 
                {{ old('game_type') === '10_ball' ? 'checked' : '' }}
                onchange="updateGameplaySelectionUI()"
              >
              <div class="flex items-center justify-between mb-1">
                <span class="text-lg">🔴</span>
                <span class="badge-brutal px-1.5 py-0.5 bg-gray-200 text-gray-900 text-[9px]">FRAME</span>
              </div>
              <div class="font-black text-sm uppercase text-gray-950">10 Ball</div>
              <div class="text-[11px] font-bold text-gray-800 mt-1 game-rate-display" data-game="10_ball">
                Rs. 150 / frame
              </div>
            </label>

            <!-- One Ball -->
            <label class="cursor-pointer border-2 border-black p-3.5 block relative select-none gameplay-choice bg-gray-50 hover:bg-gray-100 transition">
              <input 
                type="radio" 
                name="game_type" 
                value="one_ball" 
                class="sr-only" 
                {{ old('game_type') === 'one_ball' ? 'checked' : '' }}
                onchange="updateGameplaySelectionUI()"
              >
              <div class="flex items-center justify-between mb-1">
                <span class="text-lg">🟡</span>
                <span class="badge-brutal px-1.5 py-0.5 bg-gray-200 text-gray-900 text-[9px]">FRAME</span>
              </div>
              <div class="font-black text-sm uppercase text-gray-950">One Ball</div>
              <div class="text-[11px] font-bold text-gray-800 mt-1 game-rate-display" data-game="one_ball">
                Rs. 120 / frame
              </div>
            </label>

          </div>
        </div>

        <!-- 3. Table Selection (with table-specific rates embedded) -->
        <div class="space-y-2">
          <label class="block text-xs font-black uppercase tracking-wider text-gray-950">
            3. Select Table <span class="text-red-600">*</span>
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($availableTables as $tbl)
              <label 
                class="cursor-pointer border-2 border-black p-3 block relative select-none table-choice {{ (old('table_id', $selectedTableId) == $tbl->id || ($loop->first && !$selectedTableId)) ? 'bg-brand-yellow shadow-brutal-sm ring-2 ring-black' : 'bg-gray-50 hover:bg-gray-100' }}"
                data-table-id="{{ $tbl->id }}"
                data-table-name="{{ $tbl->name }}"
                data-century="{{ $tbl->getRateForGame('century') }}"
                data-6ball="{{ $tbl->getRateForGame('6_ball') }}"
                data-10ball="{{ $tbl->getRateForGame('10_ball') }}"
                data-oneball="{{ $tbl->getRateForGame('one_ball') }}"
              >
                <input 
                  type="radio" 
                  name="table_id" 
                  value="{{ $tbl->id }}" 
                  class="sr-only" 
                  {{ (old('table_id', $selectedTableId) == $tbl->id || ($loop->first && !$selectedTableId)) ? 'checked' : '' }}
                  onchange="updateTableSelectionUI()"
                >
                <div class="font-black text-sm uppercase text-gray-950">{{ $tbl->name }}</div>
                <div class="text-[11px] font-bold text-gray-600 uppercase">{{ $tbl->type }}</div>
                <span class="inline-block mt-2 badge-brutal px-1.5 py-0.5 bg-green-100 text-green-950 border-green-700 text-[9px]">
                  AVAILABLE
                </span>
              </label>
            @endforeach
          </div>
        </div>

        <!-- 4. Dynamic Billing Summary Box -->
        <div class="card-brutal p-4 bg-gray-50 border-2 border-black space-y-2">
          <div class="flex items-center justify-between text-xs font-bold text-gray-700">
            <span class="uppercase">Selected Table:</span>
            <span class="font-bold text-gray-950" id="summary-table-name">—</span>
          </div>
          <div class="flex items-center justify-between text-xs font-bold text-gray-700">
            <span class="uppercase">Gameplay Mode:</span>
            <span class="font-bold text-gray-950" id="summary-game-name">—</span>
          </div>
          <div class="flex items-center justify-between text-xs font-bold text-gray-700">
            <span class="uppercase">Billing Rate:</span>
            <span class="font-mono font-black text-gray-950 text-sm" id="summary-rate">—</span>
          </div>
          <div class="border-t-2 border-black pt-2 flex items-center justify-between text-sm font-black text-gray-950">
            <span class="uppercase tracking-wide">Initial Price:</span>
            <span class="font-mono text-lg text-black" id="summary-starting-price">—</span>
          </div>
          <p class="text-[11px] text-gray-600 font-bold mt-1" id="summary-note">
            <!-- Dynamic note injected by JS -->
          </p>
        </div>

        <!-- 5. Optional Notes -->
        <div class="space-y-1.5">
          <label for="notes" class="block text-xs font-black uppercase tracking-wider text-gray-950">
            Session Notes / Cues <span class="text-gray-500 text-[10px] lowercase font-normal">(optional)</span>
          </label>
          <input 
            type="text" 
            name="notes" 
            id="notes" 
            value="{{ old('notes') }}"
            placeholder="e.g. VIP cue sticks, referee notes, etc." 
            class="w-full input-brutal px-4 py-2 text-xs text-gray-950"
          >
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
          <button type="submit" class="w-full btn-brutal py-4 bg-brand-yellow hover:bg-yellow-400 text-black text-base font-black uppercase tracking-wider flex items-center justify-center gap-2">
            <i class="fa-solid fa-play"></i>
            <span>START SESSION</span>
          </button>
        </div>
      </form>
    </div>
  @endif

</div>

@push('scripts')
<script>
  function getSelectedTableElement() {
    const checkedRadio = document.querySelector('input[name="table_id"]:checked');
    if (!checkedRadio) return null;
    return checkedRadio.closest('.table-choice');
  }

  function getSelectedGameType() {
    const checkedRadio = document.querySelector('input[name="game_type"]:checked');
    return checkedRadio ? checkedRadio.value : 'century';
  }

  function updateTableSelectionUI() {
    document.querySelectorAll('.table-choice').forEach(card => {
      const radio = card.querySelector('input[type="radio"]');
      if (radio.checked) {
        card.classList.add('bg-brand-yellow', 'shadow-brutal-sm', 'ring-2', 'ring-black');
        card.classList.remove('bg-gray-50');
      } else {
        card.classList.remove('bg-brand-yellow', 'shadow-brutal-sm', 'ring-2', 'ring-black');
        card.classList.add('bg-gray-50');
      }
    });

    updateSummaryCalculations();
  }

  function updateGameplaySelectionUI() {
    document.querySelectorAll('.gameplay-choice').forEach(card => {
      const radio = card.querySelector('input[type="radio"]');
      if (radio.checked) {
        card.classList.add('bg-brand-yellow', 'shadow-brutal-sm', 'ring-2', 'ring-black');
        card.classList.remove('bg-gray-50');
      } else {
        card.classList.remove('bg-brand-yellow', 'shadow-brutal-sm', 'ring-2', 'ring-black');
        card.classList.add('bg-gray-50');
      }
    });

    updateSummaryCalculations();
  }

  function updateSummaryCalculations() {
    const tableEl = getSelectedTableElement();
    const gameType = getSelectedGameType();

    if (!tableEl) return;

    const tableName = tableEl.dataset.tableName || 'Selected Table';
    let rate = 0;
    let unit = '';
    let gameTitle = '';
    let note = '';

    if (gameType === 'century') {
      rate = parseFloat(tableEl.dataset.century) || 10;
      unit = 'Rs. ' + rate.toFixed(0) + ' / minute';
      gameTitle = 'Century (Time-Based)';
      note = '⏱️ Timer runs continuously. Billed at Rs. ' + rate.toFixed(0) + ' per elapsed minute.';
    } else if (gameType === '6_ball') {
      rate = parseFloat(tableEl.dataset['6ball']) || 130;
      unit = 'Rs. ' + rate.toFixed(0) + ' / frame';
      gameTitle = '6 Ball (Frame-Based)';
      note = '🎱 Standard 6-red frame. You can add more frames during play.';
    } else if (gameType === '10_ball') {
      rate = parseFloat(tableEl.dataset['10ball']) || 150;
      unit = 'Rs. ' + rate.toFixed(0) + ' / frame';
      gameTitle = '10 Ball (Frame-Based)';
      note = '🔴 10-red frame. You can add more frames during play.';
    } else if (gameType === 'one_ball') {
      rate = parseFloat(tableEl.dataset.oneball) || 120;
      unit = 'Rs. ' + rate.toFixed(0) + ' / frame';
      gameTitle = 'One Ball (Frame-Based)';
      note = '🟡 Single ball frame. You can add more frames during play.';
    }

    // Update gameplay card rate displays based on current selected table
    const centuryCardRate = document.querySelector('.game-rate-display[data-game="century"]');
    if (centuryCardRate) centuryCardRate.innerText = 'Rs. ' + (parseFloat(tableEl.dataset.century) || 10).toFixed(0) + ' / min';

    const ball6CardRate = document.querySelector('.game-rate-display[data-game="6_ball"]');
    if (ball6CardRate) ball6CardRate.innerText = 'Rs. ' + (parseFloat(tableEl.dataset['6ball']) || 130).toFixed(0) + ' / frame';

    const ball10CardRate = document.querySelector('.game-rate-display[data-game="10_ball"]');
    if (ball10CardRate) ball10CardRate.innerText = 'Rs. ' + (parseFloat(tableEl.dataset['10ball']) || 150).toFixed(0) + ' / frame';

    const oneBallCardRate = document.querySelector('.game-rate-display[data-game="one_ball"]');
    if (oneBallCardRate) oneBallCardRate.innerText = 'Rs. ' + (parseFloat(tableEl.dataset.oneball) || 120).toFixed(0) + ' / frame';

    // Update Summary Box
    document.getElementById('summary-table-name').innerText = tableName;
    document.getElementById('summary-game-name').innerText = gameTitle;
    document.getElementById('summary-rate').innerText = unit;
    document.getElementById('summary-starting-price').innerText = 'Rs. ' + rate.toFixed(0);
    document.getElementById('summary-note').innerText = note;
  }

  // Initial Run
  document.addEventListener('DOMContentLoaded', () => {
    updateTableSelectionUI();
    updateGameplaySelectionUI();
  });
</script>
@endpush
@endsection
