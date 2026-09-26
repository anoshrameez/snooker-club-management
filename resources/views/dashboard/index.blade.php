@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

  <!-- TOP HEADER & NEW SESSION BUTTON -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b-2 border-black">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <span>Reception Dashboard</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-0.5">
        Live Counter • {{ \Carbon\Carbon::now()->format('l, d M Y') }}
      </p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('sessions.create') }}" class="btn-brutal px-6 py-3 bg-brand-yellow hover:bg-yellow-400 text-black text-sm uppercase tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-plus text-base"></i>
        <span>+ NEW SESSION</span>
      </a>
    </div>
  </div>

  <!-- ACTIVE SESSION RECOVERY BANNER (IF ANY ACTIVE SESSIONS EXIST) -->
  @if($activeSessions->isNotEmpty())
    @php $primaryActive = $activeSessions->first(); @endphp
    <div class="card-brutal p-5 bg-amber-50 border-amber-600 border-2 shadow-brutal">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 bg-brand-yellow border-2 border-black flex items-center justify-center text-lg shrink-0 mt-0.5 shadow-brutal-sm">
            <i class="fa-solid fa-bell text-black animate-pulse"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="badge-brutal px-2 py-0.5 bg-black text-white text-[11px]">ACTIVE SESSION FOUND</span>
              <span class="font-bold text-xs text-amber-900">{{ $activeSessions->count() }} table(s) in play</span>
            </div>
            <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
              <div>
                <span class="text-gray-500 font-bold block uppercase text-[10px]">Customer:</span>
                <span class="font-black text-sm text-gray-950">{{ $primaryActive->customer->name ?? 'Guest' }}</span>
              </div>
              <div>
                <span class="text-gray-500 font-bold block uppercase text-[10px]">Table:</span>
                <span class="font-black text-sm text-gray-950">{{ $primaryActive->table->name ?? 'Table' }}</span>
              </div>
              <div>
                <span class="text-gray-500 font-bold block uppercase text-[10px]">Rounds:</span>
                <span class="font-black text-sm text-gray-950">{{ $primaryActive->rounds }} Frame(s)</span>
              </div>
              <div>
                <span class="text-gray-500 font-bold block uppercase text-[10px]">Live Playing Time:</span>
                <span class="font-mono font-black text-sm text-amber-700 recovery-timer" data-start="{{ $primaryActive->start_time->toISOString() }}">
                  {{ $primaryActive->timerClockString() }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('sessions.show', $primaryActive->id) }}" class="btn-brutal px-5 py-2.5 bg-brand-yellow hover:bg-yellow-400 text-black text-xs font-black uppercase tracking-wide flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-right"></i>
            <span>CONTINUE SESSION</span>
          </a>
          <form action="{{ route('sessions.cancel', $primaryActive->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this active session? Table will be released.')">
            @csrf
            <button type="submit" class="btn-brutal px-3 py-2.5 bg-white hover:bg-red-50 text-red-600 text-xs font-bold uppercase">
              CANCEL
            </button>
          </form>
        </div>
      </div>
    </div>
  @endif

  <!-- SECTION 6: TODAY'S STATISTICS -->
  <div class="space-y-2">
    <h2 class="text-xs font-black uppercase tracking-widest text-gray-700 flex items-center gap-1.5">
      <i class="fa-solid fa-calendar-day"></i>
      <span>TODAY'S OVERVIEW</span>
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
      <!-- Sessions -->
      <div class="card-brutal p-4 bg-white">
        <p class="text-[11px] font-black uppercase text-gray-500 tracking-wider">Sessions</p>
        <p class="text-3xl font-black text-gray-950 font-mono mt-1">{{ $totalSessions }}</p>
        <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">Today</p>
      </div>

      <!-- Rounds -->
      <div class="card-brutal p-4 bg-white">
        <p class="text-[11px] font-black uppercase text-gray-500 tracking-wider">Rounds</p>
        <p class="text-3xl font-black text-gray-950 font-mono mt-1">{{ $totalRounds }}</p>
        <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">Frames played</p>
      </div>

      <!-- Revenue -->
      <div class="card-brutal p-4 bg-white border-brand-yellow">
        <p class="text-[11px] font-black uppercase text-gray-500 tracking-wider">Revenue</p>
        <p class="text-2xl sm:text-3xl font-black text-gray-950 font-mono mt-1 truncate" title="{{ $currency }} {{ number_format($totalRevenue, 0) }}">
          {{ $currency }} {{ number_format($totalRevenue, 0) }}
        </p>
        <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">Total sales</p>
      </div>

      <!-- Paid -->
      <div class="card-brutal p-4 bg-green-50 border-green-700">
        <p class="text-[11px] font-black uppercase text-green-800 tracking-wider">Paid</p>
        <p class="text-2xl sm:text-3xl font-black text-green-900 font-mono mt-1 truncate">
          {{ $currency }} {{ number_format($paidAmount, 0) }}
        </p>
        <p class="text-[10px] font-bold text-green-700 mt-1 uppercase">Received</p>
      </div>

      <!-- Unpaid -->
      <div class="card-brutal p-4 bg-red-50 border-red-700">
        <p class="text-[11px] font-black uppercase text-red-800 tracking-wider">Unpaid</p>
        <p class="text-2xl sm:text-3xl font-black text-red-900 font-mono mt-1 truncate">
          {{ $currency }} {{ number_format($unpaidAmount, 0) }}
        </p>
        <p class="text-[10px] font-bold text-red-700 mt-1 uppercase">Outstanding</p>
      </div>

      <!-- Active Tables -->
      <div class="card-brutal p-4 bg-amber-50 border-black">
        <p class="text-[11px] font-black uppercase text-gray-700 tracking-wider">Active Tables</p>
        <p class="text-3xl font-black text-amber-600 font-mono mt-1">{{ $activeTablesCount }}</p>
        <p class="text-[10px] font-bold text-gray-500 mt-1 uppercase">In play right now</p>
      </div>
    </div>
  </div>

  <!-- ACTIVE TABLES GRID -->
  <div class="space-y-3 pt-2" id="active-tables">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-black uppercase tracking-wider text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-border-all"></i>
        <span>Tables Live Status</span>
      </h2>
      <span class="text-xs font-bold text-gray-500">
        Click any occupied table to open & control session
      </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      @forelse($tables as $table)
        @php
          $isOccupied = $table->status === 'occupied' && $table->currentSession;
          $isMaintenance = $table->status === 'maintenance';
          $sess = $table->currentSession;
        @endphp

        @if($isOccupied)
          <!-- OCCUPIED TABLE CARD -->
          <a href="{{ route('sessions.show', $sess->id) }}" class="card-brutal p-5 bg-[#FEF9C3] hover:bg-yellow-100 transition block relative group">
            <div class="flex items-start justify-between border-b-2 border-black pb-3">
              <div>
                <h3 class="font-black text-lg text-black uppercase tracking-tight flex items-center gap-2">
                  <span>{{ $table->name }}</span>
                </h3>
                <p class="text-xs font-bold text-gray-600 uppercase">{{ $table->type }}</p>
              </div>
              <span class="badge-brutal px-2.5 py-1 bg-black text-brand-yellow text-xs animate-pulse">
                IN PLAY
              </span>
            </div>

            <!-- Customer & Live Timer Info -->
            <div class="py-4 space-y-2.5">
              <div class="flex justify-between items-center text-sm">
                <span class="text-xs font-bold uppercase text-gray-600">Player:</span>
                <span class="font-black text-base text-gray-950">{{ $sess->customer->name ?? 'Guest' }}</span>
              </div>

              <div class="flex justify-between items-center text-sm">
                <span class="text-xs font-bold uppercase text-gray-600">Live Playing Time:</span>
                <span class="font-mono font-bold text-sm text-gray-950 live-dashboard-timer" data-start="{{ $sess->start_time->toISOString() }}">
                  {{ $sess->formattedDuration() }}
                </span>
              </div>

              <div class="flex justify-between items-center text-sm">
                <span class="text-xs font-bold uppercase text-gray-600">Rounds / Frames:</span>
                <span class="font-mono font-bold text-sm text-gray-950">{{ $sess->rounds }} frame(s)</span>
              </div>

              <div class="pt-2 border-t-2 border-black/20 flex justify-between items-center">
                <span class="text-xs font-black uppercase text-gray-700">Running Total:</span>
                <span class="font-mono font-black text-lg text-black">{{ $currency }} {{ number_format($sess->total_price, 0) }}</span>
              </div>
            </div>

            <div class="pt-2">
              <div class="btn-brutal w-full py-2 bg-black text-white text-xs font-black uppercase text-center flex items-center justify-center gap-2 group-hover:bg-gray-900">
                <span>Manage Table</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
              </div>
            </div>
          </a>

        @elseif($isMaintenance)
          <!-- MAINTENANCE TABLE -->
          <div class="card-brutal p-5 bg-gray-100 opacity-75">
            <div class="flex items-start justify-between border-b-2 border-black pb-3">
              <div>
                <h3 class="font-black text-lg text-gray-700 uppercase tracking-tight">{{ $table->name }}</h3>
                <p class="text-xs font-bold text-gray-500 uppercase">{{ $table->type }}</p>
              </div>
              <span class="badge-brutal px-2.5 py-1 bg-gray-300 text-gray-800 text-xs">
                MAINTENANCE
              </span>
            </div>
            <div class="py-6 text-center text-xs font-bold text-gray-500 uppercase">
              <i class="fa-solid fa-wrench text-2xl mb-2 text-gray-400 block"></i>
              Under Servicing / Felt Care
            </div>
          </div>

        @else
          <!-- AVAILABLE TABLE CARD -->
          <div class="card-brutal p-5 bg-white flex flex-col justify-between">
            <div>
              <div class="flex items-start justify-between border-b-2 border-black pb-3">
                <div>
                  <h3 class="font-black text-lg text-gray-900 uppercase tracking-tight">{{ $table->name }}</h3>
                  <p class="text-xs font-bold text-gray-500 uppercase">{{ $table->type }}</p>
                </div>
                <span class="badge-brutal px-2.5 py-1 bg-green-100 text-green-900 border-green-700 text-xs">
                  AVAILABLE
                </span>
              </div>

              <div class="py-4 space-y-1 text-xs">
                <div class="flex justify-between text-gray-600">
                  <span class="font-bold uppercase">Rate per Frame:</span>
                  <span class="font-mono font-bold text-gray-900">{{ $currency }} {{ number_format($pricePerRound, 0) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                  <span class="font-bold uppercase">Status:</span>
                  <span class="font-bold text-green-700">Ready for play</span>
                </div>
              </div>
            </div>

            <div class="pt-2">
              <a href="{{ route('sessions.create', ['table_id' => $table->id]) }}" class="btn-brutal w-full py-2 bg-brand-yellow hover:bg-yellow-400 text-black text-xs font-black uppercase text-center block">
                + START SESSION
              </a>
            </div>
          </div>
        @endif
      @empty
        <div class="col-span-full card-brutal p-8 text-center bg-white">
          <p class="font-bold text-sm text-gray-600">No tables configured in system.</p>
        </div>
      @endforelse
    </div>
  </div>

  <!-- RECENT SESSIONS TABLE -->
  <div class="card-brutal p-5 bg-white space-y-4">
    <div class="flex items-center justify-between border-b-2 border-black pb-3">
      <div>
        <h3 class="font-black text-base text-gray-950 uppercase tracking-tight flex items-center gap-2">
          <i class="fa-solid fa-list-check"></i>
          <span>Recent Completed Sessions Today</span>
        </h3>
      </div>
      <a href="{{ route('sessions.index') }}" class="text-xs font-black uppercase tracking-wider text-black underline hover:text-gray-700">
        View All History ➔
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b-2 border-black bg-gray-100 text-gray-900 uppercase font-black tracking-wider">
            <th class="p-3">Session Code</th>
            <th class="p-3">Customer</th>
            <th class="p-3">Table</th>
            <th class="p-3">Frames</th>
            <th class="p-3">Duration</th>
            <th class="p-3">Total</th>
            <th class="p-3">Payment</th>
            <th class="p-3 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-gray-200 font-medium">
          @forelse($recentSessions as $rs)
            <tr class="hover:bg-gray-50">
              <td class="p-3 font-mono font-bold">{{ $rs->session_code }}</td>
              <td class="p-3 font-bold text-gray-950">{{ $rs->customer->name ?? 'Guest' }}</td>
              <td class="p-3 font-bold">{{ $rs->table->name ?? 'Table' }}</td>
              <td class="p-3 font-mono">{{ $rs->rounds }}</td>
              <td class="p-3 font-mono">{{ $rs->formattedDuration() }}</td>
              <td class="p-3 font-mono font-bold">{{ $currency }} {{ number_format($rs->total_price, 0) }}</td>
              <td class="p-3">
                <span class="badge-brutal px-2 py-0.5 text-[10px] {{ $rs->isPaid() ? 'bg-green-100 text-green-950 border-green-700' : 'bg-red-100 text-red-950 border-red-700' }}">
                  {{ $rs->payment_status }}
                </span>
              </td>
              <td class="p-3 text-right">
                <a href="{{ route('sessions.show', $rs->id) }}" class="btn-brutal px-2.5 py-1 bg-white hover:bg-gray-100 text-xs font-bold">
                  Receipt
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-6 text-center text-gray-500 font-bold">
                No sessions completed yet today.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

@push('scripts')
<script>
  // Live ticker for active tables on dashboard
  function updateDashboardTimers() {
    const timers = document.querySelectorAll('.live-dashboard-timer, .recovery-timer');
    const now = new Date().getTime();

    timers.forEach(t => {
      const startStr = t.getAttribute('data-start');
      if (!startStr) return;
      const start = new Date(startStr).getTime();
      const diffSec = Math.max(0, Math.floor((now - start) / 1000));

      const hrs = Math.floor(diffSec / 3600);
      const mins = Math.floor((diffSec % 3600) / 60);
      const secs = diffSec % 60;

      if (t.classList.contains('recovery-timer')) {
        t.innerText = String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
      } else {
        t.innerText = hrs > 0 ? `${hrs}h ${mins}m` : `${mins} min`;
      }
    });
  }

  setInterval(updateDashboardTimers, 1000);
  updateDashboardTimers();
</script>
@endpush
@endsection
