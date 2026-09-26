@extends('layouts.app')

@section('title', 'Admin Settings — Club Pricing')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <!-- Header -->
  <div class="pb-3 border-b-2 border-black flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-sliders text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Club Pricing & Configuration</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Administrator Settings • Rate Per Frame & Business Profile
      </p>
    </div>
    <span class="badge-brutal px-2.5 py-1 bg-black text-brand-yellow text-xs">
      ADMIN ONLY
    </span>
  </div>

  <!-- Settings Form Card (Section 9) -->
  <div class="card-brutal p-6 sm:p-8 bg-white space-y-6">
    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
      @csrf

      <!-- SECTION 9: Price Per Round -->
      <div class="p-5 bg-yellow-50 border-2 border-black space-y-2">
        <label for="price_per_round" class="block text-xs font-black uppercase tracking-wider text-gray-950 flex justify-between items-center">
          <span>Standard Price Per Round / Frame <span class="text-red-600">*</span></span>
          <span class="text-[10px] font-mono font-bold bg-black text-white px-2 py-0.5">CURRENT: {{ $settings['currency'] }} {{ $settings['price_per_round'] }}</span>
        </label>
        <div class="flex items-center">
          <span class="inline-flex items-center px-4 py-3 border-2 border-r-0 border-black bg-gray-100 text-sm font-black font-mono">
            {{ $settings['currency'] }}
          </span>
          <input 
            type="number" 
            step="1" 
            min="1" 
            name="price_per_round" 
            id="price_per_round" 
            required 
            value="{{ old('price_per_round', $settings['price_per_round']) }}" 
            class="w-full input-brutal px-4 py-3 text-lg font-mono font-black text-gray-950"
            placeholder="500"
          >
        </div>
        <p class="text-[11px] font-bold text-gray-600">
          💡 <strong>Historical Pricing Integrity:</strong> Changing this value will apply exclusively to future game sessions. All existing and completed sessions will retain their locked historical rates.
        </p>
      </div>

      <!-- Club Details -->
      <div class="space-y-4">
        <div>
          <label for="club_name" class="block text-xs font-black uppercase tracking-wider text-gray-950 mb-1">
            Club Business Name *
          </label>
          <input 
            type="text" 
            name="club_name" 
            id="club_name" 
            required 
            value="{{ old('club_name', $settings['club_name']) }}" 
            class="w-full input-brutal px-4 py-2.5 text-sm font-bold text-gray-950"
          >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="currency" class="block text-xs font-black uppercase tracking-wider text-gray-950 mb-1">
              Currency Symbol / Prefix *
            </label>
            <input 
              type="text" 
              name="currency" 
              id="currency" 
              required 
              value="{{ old('currency', $settings['currency']) }}" 
              class="w-full input-brutal px-4 py-2.5 text-sm font-mono font-bold text-gray-950"
              placeholder="Rs."
            >
          </div>

          <div>
            <label for="club_phone" class="block text-xs font-black uppercase tracking-wider text-gray-950 mb-1">
              Reception Phone Number
            </label>
            <input 
              type="text" 
              name="club_phone" 
              id="club_phone" 
              value="{{ old('club_phone', $settings['club_phone']) }}" 
              class="w-full input-brutal px-4 py-2.5 text-sm font-mono text-gray-950"
              placeholder="+92 300 1234567"
            >
          </div>
        </div>

        <div>
          <label for="club_address" class="block text-xs font-black uppercase tracking-wider text-gray-950 mb-1">
            Physical Address (Prints on Receipt)
          </label>
          <input 
            type="text" 
            name="club_address" 
            id="club_address" 
            value="{{ old('club_address', $settings['club_address']) }}" 
            class="w-full input-brutal px-4 py-2.5 text-xs text-gray-950"
            placeholder="Commercial Phase 4, Lahore"
          >
        </div>
      </div>

      <!-- Save Button -->
      <div class="pt-2 border-t-2 border-black">
        <button type="submit" class="w-full btn-brutal py-4 bg-brand-yellow hover:bg-yellow-400 text-black text-sm font-black uppercase tracking-wider flex items-center justify-center gap-2">
          <i class="fa-solid fa-floppy-disk text-base"></i>
          <span>SAVE SETTINGS</span>
        </button>
      </div>

    </form>
  </div>

</div>
@endsection
