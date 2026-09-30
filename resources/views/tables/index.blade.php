@extends('layouts.app')

@section('title', 'Snooker & Pool Tables')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b-2 border-black">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-border-all text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Tables & Pricing Management</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Configure Table Inventory & Custom Pricing for All 4 Gameplays
      </p>
    </div>

    @if(auth()->user()->isAdmin())
      <button onclick="document.getElementById('add-table-box').classList.toggle('hidden')" class="btn-brutal px-4 py-2 bg-brand-yellow hover:bg-yellow-400 text-black text-xs uppercase font-black flex items-center gap-1.5 self-start sm:self-auto">
        <i class="fa-solid fa-plus"></i>
        <span>+ Add New Table</span>
      </button>
    @endif
  </div>

  <!-- Add Table Card (Admin Only) -->
  @if(auth()->user()->isAdmin())
    <div id="add-table-box" class="hidden card-brutal p-6 bg-yellow-50 border-2 border-black space-y-4">
      <div class="flex items-center justify-between border-b-2 border-black pb-2">
        <h3 class="text-sm font-black uppercase tracking-wider text-gray-950 flex items-center gap-1.5">
          <i class="fa-solid fa-plus"></i>
          <span>Add New Snooker Table with Custom Rates</span>
        </h3>
        <button type="button" onclick="document.getElementById('add-table-box').classList.add('hidden')" class="text-xs font-black uppercase hover:text-red-600">✕ Close</button>
      </div>

      <form action="{{ route('tables.store', [], false) }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block text-[10px] font-black uppercase text-gray-700 mb-1">Table Name *</label>
            <input type="text" name="name" required placeholder="e.g. Table 7" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase text-gray-700 mb-1">Table Type *</label>
            <select name="type" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
              <option value="Standard Snooker">Standard Snooker</option>
              <option value="Tournament Match">Tournament Match</option>
              <option value="VIP Lounge">VIP Lounge</option>
              <option value="8-Ball American Pool">8-Ball American Pool</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase text-gray-700 mb-1">Initial Status</label>
            <select name="status" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
              <option value="available">Available</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
        </div>

        <!-- 4 Gameplays Pricing for this new table -->
        <div class="p-3 bg-white border-2 border-black space-y-2">
          <label class="block text-xs font-black uppercase tracking-wider text-gray-950">
            Set Rates For This Table (PKR)
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
              <span class="text-[10px] font-bold text-gray-600 block uppercase">Century (Per Min):</span>
              <input type="number" step="1" min="1" name="rate_century" value="10" class="w-full input-brutal px-3 py-1.5 text-xs font-mono font-bold" required>
            </div>
            <div>
              <span class="text-[10px] font-bold text-gray-600 block uppercase">6 Ball (Per Frame):</span>
              <input type="number" step="1" min="1" name="rate_6ball" value="130" class="w-full input-brutal px-3 py-1.5 text-xs font-mono font-bold" required>
            </div>
            <div>
              <span class="text-[10px] font-bold text-gray-600 block uppercase">10 Ball (Per Frame):</span>
              <input type="number" step="1" min="1" name="rate_10ball" value="150" class="w-full input-brutal px-3 py-1.5 text-xs font-mono font-bold" required>
            </div>
            <div>
              <span class="text-[10px] font-bold text-gray-600 block uppercase">One Ball (Per Frame):</span>
              <input type="number" step="1" min="1" name="rate_oneball" value="120" class="w-full input-brutal px-3 py-1.5 text-xs font-mono font-bold" required>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-1">
          <button type="button" onclick="document.getElementById('add-table-box').classList.add('hidden')" class="btn-brutal px-4 py-1.5 bg-white text-xs font-bold">
            Cancel
          </button>
          <button type="submit" class="btn-brutal px-6 py-1.5 bg-brand-yellow text-black text-xs font-black uppercase">
            Create Table
          </button>
        </div>
      </form>
    </div>
  @endif

  <!-- Tables Grid List -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($tables as $table)
      @php
        $isOccupied = $table->status === 'occupied' && $table->currentSession;
        $isMaintenance = $table->status === 'maintenance';
        $sess = $table->currentSession;
      @endphp

      <div class="card-brutal p-5 bg-white flex flex-col justify-between {{ $isOccupied ? 'border-amber-500 bg-amber-50/30' : '' }}">
        <div>
          <!-- Header -->
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

          <!-- Active Session Info or Idle Info -->
          <div class="py-3">
            @if($isOccupied)
              <div class="p-3 bg-yellow-100 border border-black space-y-1 text-xs">
                <div class="flex justify-between">
                  <span class="font-bold text-gray-600 uppercase text-[10px]">Customer:</span>
                  <span class="font-black text-black">{{ $sess->customer->name ?? 'Guest' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-bold text-gray-600 uppercase text-[10px]">Gameplay:</span>
                  <span class="font-black text-black">{{ $sess->gameTitle() }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-bold text-gray-600 uppercase text-[10px]">Session:</span>
                  <span class="font-mono font-bold text-black">#{{ $sess->session_code }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-bold text-gray-600 uppercase text-[10px]">Duration:</span>
                  <span class="font-mono font-bold text-black">{{ $sess->formattedDuration() }}</span>
                </div>
              </div>
            @endif

            <!-- 4 Gameplay Rates Grid for This Specific Table -->
            <div class="mt-3 p-3 bg-gray-50 border border-black space-y-2">
              <span class="text-[10px] font-black uppercase text-gray-600 block tracking-wider">
                Table Specific Rates (PKR):
              </span>
              <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                <div class="flex justify-between items-center bg-white p-1.5 border border-gray-300">
                  <span class="text-gray-600 text-[11px]">⏱️ Century:</span>
                  <span class="font-mono font-black text-gray-950">Rs. {{ number_format($table->getRateForGame('century'), 0) }}/m</span>
                </div>
                <div class="flex justify-between items-center bg-white p-1.5 border border-gray-300">
                  <span class="text-gray-600 text-[11px]">🎱 6 Ball:</span>
                  <span class="font-mono font-black text-gray-950">Rs. {{ number_format($table->getRateForGame('6_ball'), 0) }}</span>
                </div>
                <div class="flex justify-between items-center bg-white p-1.5 border border-gray-300">
                  <span class="text-gray-600 text-[11px]">🔴 10 Ball:</span>
                  <span class="font-mono font-black text-gray-950">Rs. {{ number_format($table->getRateForGame('10_ball'), 0) }}</span>
                </div>
                <div class="flex justify-between items-center bg-white p-1.5 border border-gray-300">
                  <span class="text-gray-600 text-[11px]">🟡 One Ball:</span>
                  <span class="font-mono font-black text-gray-950">Rs. {{ number_format($table->getRateForGame('one_ball'), 0) }}</span>
                </div>
              </div>
            </div>

            <!-- Edit Rates Drawer (Admin Only) -->
            @if(auth()->user()->isAdmin())
              <div id="edit-table-{{ $table->id }}" class="hidden mt-3 p-3 bg-yellow-50 border-2 border-black space-y-3">
                <form action="{{ route('tables.update', $table->id, false) }}" method="POST" class="space-y-2 text-xs">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="name" value="{{ $table->name }}">
                  <input type="hidden" name="type" value="{{ $table->type }}">
                  <input type="hidden" name="status" value="{{ $table->status }}">

                  <span class="font-black uppercase tracking-wider text-gray-950 block text-[11px]">
                    Update Rates for {{ $table->name }}
                  </span>

                  <div class="grid grid-cols-2 gap-2">
                    <div>
                      <span class="text-[10px] font-bold text-gray-600 block">Century (min):</span>
                      <input type="number" name="rate_century" value="{{ $table->rate_century ?? 10 }}" class="w-full input-brutal px-2 py-1 font-mono font-bold" required>
                    </div>
                    <div>
                      <span class="text-[10px] font-bold text-gray-600 block">6 Ball (frame):</span>
                      <input type="number" name="rate_6ball" value="{{ $table->rate_6ball ?? 130 }}" class="w-full input-brutal px-2 py-1 font-mono font-bold" required>
                    </div>
                    <div>
                      <span class="text-[10px] font-bold text-gray-600 block">10 Ball (frame):</span>
                      <input type="number" name="rate_10ball" value="{{ $table->rate_10ball ?? 150 }}" class="w-full input-brutal px-2 py-1 font-mono font-bold" required>
                    </div>
                    <div>
                      <span class="text-[10px] font-bold text-gray-600 block">One Ball (frame):</span>
                      <input type="number" name="rate_oneball" value="{{ $table->rate_oneball ?? 120 }}" class="w-full input-brutal px-2 py-1 font-mono font-bold" required>
                    </div>
                  </div>

                  <div class="flex justify-end gap-1.5 pt-1">
                    <button type="button" onclick="document.getElementById('edit-table-{{ $table->id }}').classList.add('hidden')" class="btn-brutal px-2.5 py-1 bg-white text-[10px] font-bold">
                      Cancel
                    </button>
                    <button type="submit" class="btn-brutal px-3 py-1 bg-black text-white text-[10px] font-black uppercase">
                      Save Rates
                    </button>
                  </div>
                </form>
              </div>
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

          <div class="flex gap-2">
            @if(auth()->user()->isAdmin())
              <button type="button" onclick="document.getElementById('edit-table-{{ $table->id }}').classList.toggle('hidden')" class="flex-1 btn-brutal py-1.5 bg-white hover:bg-gray-100 text-black text-[10px] font-bold uppercase">
                <i class="fa-solid fa-pen-to-square"></i> Change Rates
              </button>

              @if(!$isOccupied)
                <form action="{{ route('tables.update', $table->id, false) }}" method="POST" class="flex-1">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="name" value="{{ $table->name }}">
                  <input type="hidden" name="type" value="{{ $table->type }}">
                  <input type="hidden" name="status" value="{{ $isMaintenance ? 'available' : 'maintenance' }}">
                  <button type="submit" class="w-full btn-brutal py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-[10px] font-bold uppercase">
                    {{ $isMaintenance ? 'Mark Available' : 'Maintenance' }}
                  </button>
                </form>
              @endif
            @endif
          </div>
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
