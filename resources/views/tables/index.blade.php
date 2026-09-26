@extends('layouts.app')

@section('title', 'Snooker & Pool Tables')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b-2 border-black">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-border-all text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Tables Management</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Configure Table Inventory, Status & Active Session Linking
      </p>
    </div>

    @if(auth()->user()->isAdmin())
      <button onclick="document.getElementById('add-table-box').classList.toggle('hidden')" class="btn-brutal px-4 py-2 bg-brand-yellow hover:bg-yellow-400 text-black text-xs uppercase font-black flex items-center gap-1.5 self-start sm:self-auto">
        <i class="fa-solid fa-plus"></i>
        <span>+ Add Table</span>
      </button>
    @endif
  </div>

  <!-- Add Table Card (Admin Only) -->
  @if(auth()->user()->isAdmin())
    <div id="add-table-box" class="hidden card-brutal p-5 bg-yellow-50 border-2 border-black space-y-3">
      <h3 class="text-xs font-black uppercase tracking-wider text-gray-950 flex items-center gap-1.5">
        <i class="fa-solid fa-plus"></i>
        <span>Add New Snooker / Pool Table</span>
      </h3>
      <form action="{{ route('tables.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @csrf
        <div>
          <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Table Name *</label>
          <input type="text" name="name" required placeholder="e.g. Table 7" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
        </div>
        <div>
          <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Game Type *</label>
          <select name="type" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
            <option value="Standard Snooker">Standard Snooker</option>
            <option value="Tournament Match">Tournament Match</option>
            <option value="8-Ball American Pool">8-Ball American Pool</option>
            <option value="English Pool">English Pool</option>
            <option value="VIP Lounge">VIP Lounge</option>
          </select>
        </div>
        <div>
          <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">Initial Status</label>
          <select name="status" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
            <option value="available">Available</option>
            <option value="maintenance">Maintenance</option>
          </select>
        </div>
        <div class="sm:col-span-3 flex justify-end gap-2 pt-1">
          <button type="button" onclick="document.getElementById('add-table-box').classList.add('hidden')" class="btn-brutal px-4 py-1.5 bg-white text-xs font-bold">
            Cancel
          </button>
          <button type="submit" class="btn-brutal px-6 py-1.5 bg-black text-white text-xs font-black uppercase">
            Save Table
          </button>
        </div>
      </form>
    </div>
  @endif

  <!-- Tables Grid List -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($tables as $table)
      @php
        $isOccupied = $table->status === 'occupied' && $table->currentSession;
        $isMaintenance = $table->status === 'maintenance';
        $sess = $table->currentSession;
      @endphp

      <div class="card-brutal p-5 bg-white flex flex-col justify-between {{ $isOccupied ? 'border-amber-500 bg-amber-50/40' : '' }}">
        <div>
          <div class="flex items-start justify-between border-b-2 border-black pb-3">
            <div>
              <h3 class="font-black text-lg text-gray-950 uppercase tracking-tight">{{ $table->name }}</h3>
              <p class="text-xs font-bold text-gray-500 uppercase">{{ $table->type }}</p>
            </div>

            @if($isOccupied)
              <span class="badge-brutal px-2.5 py-1 bg-black text-brand-yellow text-xs animate-pulse">
                IN PLAY
              </span>
            @elseif($isMaintenance)
              <span class="badge-brutal px-2.5 py-1 bg-gray-200 text-gray-800 text-xs">
                MAINTENANCE
              </span>
            @else
              <span class="badge-brutal px-2.5 py-1 bg-green-100 text-green-950 border-green-700 text-xs">
                AVAILABLE
              </span>
            @endif
          </div>

          <!-- Current Info -->
          <div class="py-4 space-y-2 text-xs">
            @if($isOccupied)
              <div class="p-3 bg-yellow-100 border border-black space-y-1">
                <div class="flex justify-between">
                  <span class="font-bold text-gray-600 uppercase text-[10px]">Active Customer:</span>
                  <span class="font-black text-black">{{ $sess->customer->name ?? 'Guest' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-bold text-gray-600 uppercase text-[10px]">Session Code:</span>
                  <span class="font-mono font-bold text-black">{{ $sess->session_code }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-bold text-gray-600 uppercase text-[10px]">Playing Time:</span>
                  <span class="font-mono font-bold text-black">{{ $sess->formattedDuration() }}</span>
                </div>
              </div>
            @else
              <p class="text-gray-500 font-bold uppercase text-[11px]">
                {{ $isMaintenance ? 'Table is offline for felt or rail maintenance' : 'Ready for player check-in' }}
              </p>
            @endif
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-3 border-t-2 border-black/20 space-y-2">
          @if($isOccupied)
            <a href="{{ route('sessions.show', $sess->id) }}" class="w-full btn-brutal py-2 bg-black text-white text-xs font-black uppercase text-center block">
              Open Session Console ➔
            </a>
          @elseif(!$isMaintenance)
            <a href="{{ route('sessions.create', ['table_id' => $table->id]) }}" class="w-full btn-brutal py-2 bg-brand-yellow hover:bg-yellow-400 text-black text-xs font-black uppercase text-center block">
              + Start Game Session
            </a>
          @endif

          <!-- Admin Quick Maintenance Toggle -->
          @if(auth()->user()->isAdmin() && !$isOccupied)
            <form action="{{ route('tables.update', $table->id) }}" method="POST">
              @csrf
              @method('PUT')
              <input type="hidden" name="name" value="{{ $table->name }}">
              <input type="hidden" name="type" value="{{ $table->type }}">
              <input type="hidden" name="status" value="{{ $isMaintenance ? 'available' : 'maintenance' }}">
              <button type="submit" class="w-full btn-brutal py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 text-[10px] font-bold uppercase">
                {{ $isMaintenance ? 'Mark Available' : 'Put on Maintenance' }}
              </button>
            </form>
          @endif
        </div>
      </div>
    @empty
      <div class="col-span-full card-brutal p-8 text-center bg-white">
        <p class="font-bold text-sm text-gray-500">No tables configured.</p>
      </div>
    @endforelse
  </div>

</div>
@endsection
