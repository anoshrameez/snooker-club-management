@extends('layouts.app')

@section('title', 'Admin Settings — Club Pricing')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  <!-- Header -->
  <div class="pb-3 border-b-2 border-black flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-sliders text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Club Settings & Default Gameplay Pricing</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Configure Standard Rates for the 4 Gameplays & Club Profile
      </p>
    </div>
    <span class="badge-brutal px-2.5 py-1 bg-black text-brand-yellow text-xs">
      ADMIN ONLY
    </span>
  </div>

  <!-- Settings Form Card -->
  <div class="card-brutal p-6 sm:p-8 bg-white space-y-6">
    <form action="{{ route('settings.update', [], false) }}" method="POST" class="space-y-6">
      @csrf

      <!-- Default 4 Gameplays Pricing -->
      <div class="p-5 bg-yellow-50 border-2 border-black space-y-3">
        <div class="flex items-center justify-between border-b border-black/20 pb-2">
          <label class="block text-xs font-black uppercase tracking-wider text-gray-950">
            Standard Default Rates for 4 Gameplays (PKR)
          </label>
          <span class="text-[10px] font-bold text-gray-500 uppercase">Used when table has no custom rate</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Century -->
          <div class="bg-white p-3 border-2 border-black space-y-1">
            <label class="block text-xs font-black uppercase text-gray-900 flex justify-between items-center">
              <span>⏱️ Century (Per Minute) *</span>
              <span class="text-[9px] bg-black text-brand-yellow px-1.5 py-0.2 badge-brutal">TIME-BASED</span>
            </label>
            <div class="flex items-center">
              <span class="px-3 py-2 border-2 border-r-0 border-black bg-gray-100 font-mono font-bold text-xs">
                Rs.
              </span>
              <input 
                type="number" 
                step="1" 
                min="1" 
                name="rate_century" 
                required 
                value="{{ old('rate_century', $settings['rate_century']) }}" 
                class="w-full input-brutal px-3 py-2 text-sm font-mono font-black text-gray-950"
              >
            </div>
            <p class="text-[10px] text-gray-500">Charged per elapsed minute of playing time.</p>
          </div>

          <!-- 6 Ball -->
          <div class="bg-white p-3 border-2 border-black space-y-1">
            <label class="block text-xs font-black uppercase text-gray-900 flex justify-between items-center">
              <span>🎱 6 Ball (Per Frame) *</span>
              <span class="text-[9px] bg-gray-200 text-gray-900 px-1.5 py-0.2 badge-brutal">FRAME-BASED</span>
            </label>
            <div class="flex items-center">
              <span class="px-3 py-2 border-2 border-r-0 border-black bg-gray-100 font-mono font-bold text-xs">
                Rs.
              </span>
              <input 
                type="number" 
                step="1" 
                min="1" 
                name="rate_6ball" 
                required 
                value="{{ old('rate_6ball', $settings['rate_6ball']) }}" 
                class="w-full input-brutal px-3 py-2 text-sm font-mono font-black text-gray-950"
              >
            </div>
            <p class="text-[10px] text-gray-500">Standard 6-red snooker frame rate.</p>
          </div>

          <!-- 10 Ball -->
          <div class="bg-white p-3 border-2 border-black space-y-1">
            <label class="block text-xs font-black uppercase text-gray-900 flex justify-between items-center">
              <span>🔴 10 Ball (Per Frame) *</span>
              <span class="text-[9px] bg-gray-200 text-gray-900 px-1.5 py-0.2 badge-brutal">FRAME-BASED</span>
            </label>
            <div class="flex items-center">
              <span class="px-3 py-2 border-2 border-r-0 border-black bg-gray-100 font-mono font-bold text-xs">
                Rs.
              </span>
              <input 
                type="number" 
                step="1" 
                min="1" 
                name="rate_10ball" 
                required 
                value="{{ old('rate_10ball', $settings['rate_10ball']) }}" 
                class="w-full input-brutal px-3 py-2 text-sm font-mono font-black text-gray-950"
              >
            </div>
            <p class="text-[10px] text-gray-500">10-red snooker frame rate.</p>
          </div>

          <!-- One Ball -->
          <div class="bg-white p-3 border-2 border-black space-y-1">
            <label class="block text-xs font-black uppercase text-gray-900 flex justify-between items-center">
              <span>🟡 One Ball (Per Frame) *</span>
              <span class="text-[9px] bg-gray-200 text-gray-900 px-1.5 py-0.2 badge-brutal">FRAME-BASED</span>
            </label>
            <div class="flex items-center">
              <span class="px-3 py-2 border-2 border-r-0 border-black bg-gray-100 font-mono font-bold text-xs">
                Rs.
              </span>
              <input 
                type="number" 
                step="1" 
                min="1" 
                name="rate_oneball" 
                required 
                value="{{ old('rate_oneball', $settings['rate_oneball']) }}" 
                class="w-full input-brutal px-3 py-2 text-sm font-mono font-black text-gray-950"
              >
            </div>
            <p class="text-[10px] text-gray-500">Single ball frame game rate.</p>
          </div>
        </div>

        <p class="text-[11px] font-bold text-gray-600 pt-1">
          💡 <em>Note:</em> Each table can also have its own individual custom pricing configured in the <a href="{{ route('tables.index') }}" class="underline font-black text-black hover:text-amber-800">Tables Management</a> screen.
        </p>
      </div>

      <!-- Club Details -->
      <div class="space-y-4">
        <div>
          <label for="club_name" class="block text-xs font-black uppercase tracking-wider text-gray-950 mb-1">
            Club Name *
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
              placeholder="0300-1234567"
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
            placeholder="Main Market, Lahore, Pakistan"
          >
        </div>
      </div>

      <!-- Save Button -->
      <div class="pt-2 border-t-2 border-black">
        <button type="submit" class="w-full btn-brutal py-4 bg-brand-yellow hover:bg-yellow-400 text-black text-sm font-black uppercase tracking-wider flex items-center justify-center gap-2">
          <i class="fa-solid fa-floppy-disk text-base"></i>
          <span>SAVE DEFAULT SETTINGS</span>
        </button>
      </div>

    </form>
  </div>

  <!-- Direct Link to Per-Table Rates -->
  <div class="card-brutal p-5 bg-white border-2 border-black flex items-center justify-between">
    <div>
      <h3 class="font-black text-sm uppercase text-gray-950">Individual Table Pricing</h3>
      <p class="text-xs font-bold text-gray-500">Configure or override rates for each individual table (Table 1, Table 2, VIP, etc.).</p>
    </div>
    <a href="{{ route('tables.index') }}" class="btn-brutal px-4 py-2 bg-black text-white text-xs font-bold uppercase shrink-0">
      Manage Tables ➔
    </a>
  </div>

</div>
@endsection
