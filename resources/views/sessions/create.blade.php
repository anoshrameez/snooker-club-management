@extends('layouts.app')

@section('title', 'New Game Session')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <!-- Breadcrumb / Header -->
  <div class="border-b-2 border-black pb-3 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-play text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Start New Game Session</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Check-In Customer & Assign Snooker Table
      </p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn-brutal px-3 py-1.5 bg-white text-xs uppercase font-bold">
      ✕ Cancel
    </a>
  </div>

  @if($availableTables->isEmpty())
    <div class="card-brutal p-6 bg-red-50 border-red-600 border-2 text-center space-y-3">
      <i class="fa-solid fa-triangle-exclamation text-3xl text-red-600"></i>
      <h2 class="text-lg font-black text-red-950 uppercase">No Tables Currently Available</h2>
      <p class="text-xs font-bold text-red-800">
        All snooker and pool tables are either occupied in active play or under maintenance. Check out an active table first to start a new game.
      </p>
      <a href="{{ route('dashboard') }}" class="btn-brutal inline-block px-5 py-2.5 bg-black text-white text-xs uppercase font-bold mt-2">
        Return to Dashboard
      </a>
    </div>
  @else

    <!-- Main New Session Form Card -->
    <div class="card-brutal p-6 sm:p-8 bg-white space-y-6">
      <form action="{{ route('sessions.store') }}" method="POST" id="new-session-form" class="space-y-6">
        @csrf

        <!-- 1. Customer Name with Auto-Suggest -->
        <div class="space-y-1.5 relative">
          <label for="customer_name" class="block text-xs font-black uppercase tracking-wider text-gray-950 flex justify-between">
            <span>1. Customer / Player Name <span class="text-red-600">*</span></span>
            <span class="text-[10px] text-gray-500 font-bold lowercase">type to search existing</span>
          </label>
          <div class="relative">
            <input 
              type="text" 
              name="customer_name" 
              id="customer_name" 
              autocomplete="off"
              required 
              value="{{ old('customer_name') }}"
              placeholder="e.g. Ahmed Khan or Team Alex" 
              class="w-full input-brutal px-4 py-3 text-base text-gray-950 font-bold"
            >
            <div id="customer-suggestions" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border-2 border-black shadow-brutal z-50 max-h-48 overflow-y-auto">
              <!-- Populated by JS -->
            </div>
          </div>

          <!-- Quick Recent Customers Chips -->
          @if($recentCustomers->isNotEmpty())
            <div class="pt-1 flex flex-wrap items-center gap-1.5">
              <span class="text-[10px] font-black uppercase text-gray-500 mr-1">Frequent:</span>
              @foreach($recentCustomers as $rc)
                <button type="button" onclick="selectCustomer('{{ addslashes($rc->name) }}', '{{ $rc->phone }}')" class="btn-brutal px-2 py-0.5 bg-gray-100 hover:bg-yellow-200 text-[11px] font-bold">
                  {{ $rc->name }}
                </button>
              @endforeach
            </div>
          @endif
        </div>

        <!-- 2. Customer Phone (Optional) -->
        <div class="space-y-1.5">
          <label for="customer_phone" class="block text-xs font-black uppercase tracking-wider text-gray-950">
            2. Phone Number <span class="text-gray-500 text-[10px] lowercase font-normal">(optional)</span>
          </label>
          <input 
            type="tel" 
            name="customer_phone" 
            id="customer_phone" 
            value="{{ old('customer_phone') }}"
            placeholder="e.g. 0300-1234567" 
            class="w-full input-brutal px-4 py-2.5 text-sm text-gray-950 font-mono"
          >
        </div>

        <!-- 3. Table Selection -->
        <div class="space-y-2">
          <label class="block text-xs font-black uppercase tracking-wider text-gray-950">
            3. Select Available Table <span class="text-red-600">*</span>
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($availableTables as $tbl)
              <label class="cursor-pointer border-2 border-black p-3 block relative transition select-none table-choice {{ (old('table_id', $selectedTableId) == $tbl->id || ($loop->first && !$selectedTableId)) ? 'bg-brand-yellow shadow-brutal-sm ring-2 ring-black' : 'bg-gray-50 hover:bg-gray-100' }}">
                <input 
                  type="radio" 
                  name="table_id" 
                  value="{{ $tbl->id }}" 
                  class="sr-only" 
                  {{ (old('table_id', $selectedTableId) == $tbl->id || ($loop->first && !$selectedTableId)) ? 'checked' : '' }}
                  onchange="updateTableSelectionUI()"
                >
                <div class="font-black text-sm uppercase text-gray-950">{{ $tbl->name }}</div>
                <div class="text-[11px] font-bold text-gray-600 mt-0.5 uppercase">{{ $tbl->type }}</div>
                <span class="inline-block mt-2 badge-brutal px-1.5 py-0.2 bg-green-100 text-green-950 border-green-700 text-[9px]">
                  AVAILABLE
                </span>
              </label>
            @endforeach
          </div>
        </div>

        <!-- Pricing Summary Box -->
        <div class="card-brutal p-4 bg-gray-50 border-2 border-black space-y-2">
          <div class="flex items-center justify-between text-xs font-bold text-gray-700">
            <span class="uppercase">Standard Rate Per Frame:</span>
            <span class="font-mono font-black text-gray-950 text-sm">{{ $currency }} {{ number_format($defaultPrice, 0) }}</span>
          </div>
          <div class="flex items-center justify-between text-xs font-bold text-gray-700">
            <span class="uppercase">Initial Frame Count:</span>
            <span class="font-mono font-bold text-gray-950 text-sm">1 Frame</span>
          </div>
          <div class="border-t-2 border-black pt-2 flex items-center justify-between text-sm font-black text-gray-950">
            <span class="uppercase tracking-wide">Starting Balance:</span>
            <span class="font-mono text-lg text-black">{{ $currency }} {{ number_format($defaultPrice, 0) }}</span>
          </div>
          <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">
            * Frame quantity can be increased anytime during active gameplay with live autosave.
          </p>
        </div>

        <!-- Optional Notes -->
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
  }

  // Quick select helper
  function selectCustomer(name, phone) {
    document.getElementById('customer_name').value = name;
    if (phone) {
      document.getElementById('customer_phone').value = phone;
    }
    document.getElementById('customer-suggestions').classList.add('hidden');
  }

  // Live Autocomplete for customer search
  const custInput = document.getElementById('customer_name');
  const suggestionsBox = document.getElementById('customer-suggestions');
  let searchTimeout = null;

  if (custInput) {
    custInput.addEventListener('input', function() {
      const query = this.value.trim();
      clearTimeout(searchTimeout);

      if (query.length < 2) {
        suggestionsBox.classList.add('hidden');
        return;
      }

      searchTimeout = setTimeout(async () => {
        try {
          const res = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(query)}`);
          const data = await res.json();

          if (data && data.length > 0) {
            suggestionsBox.innerHTML = data.map(c => `
              <div onclick="selectCustomer('${c.name.replace(/'/g, "\\'")}', '${c.phone || ''}')" class="p-2.5 hover:bg-yellow-100 cursor-pointer border-b border-gray-200 text-xs flex justify-between items-center font-bold">
                <span class="text-gray-900">${c.name}</span>
                <span class="text-gray-500 font-mono text-[11px]">${c.phone || 'No phone'}</span>
              </div>
            `).join('');
            suggestionsBox.classList.remove('hidden');
          } else {
            suggestionsBox.classList.add('hidden');
          }
        } catch (e) {
          console.error(e);
        }
      }, 250);
    });

    document.addEventListener('click', (e) => {
      if (!custInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
        suggestionsBox.classList.add('hidden');
      }
    });
  }
</script>
@endpush
@endsection
