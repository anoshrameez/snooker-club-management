@extends('layouts.app')

@section('title', 'Business Reports & Analytics')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b-2 border-black no-print">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-file-invoice-dollar text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Financial & Activity Reports</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Revenue, Frame Volumes & Table Utilization
      </p>
    </div>
    <button onclick="window.print()" class="btn-brutal px-4 py-2 bg-black text-white text-xs uppercase font-black flex items-center gap-1.5 self-start sm:self-auto">
      <i class="fa-solid fa-print"></i>
      <span>Print Summary</span>
    </button>
  </div>

  <!-- DATE RANGE FILTER FORM (SECTION 25) -->
  <div class="card-brutal p-5 bg-white space-y-3 no-print">
    <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-3">
      <div class="flex-1 w-full">
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">From Date</label>
        <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full input-brutal px-3 py-2 text-xs font-mono font-bold text-gray-950">
      </div>
      <div class="flex-1 w-full">
        <label class="block text-[10px] font-black uppercase text-gray-600 mb-1">To Date</label>
        <input type="date" name="to_date" value="{{ $toDate }}" class="w-full input-brutal px-3 py-2 text-xs font-mono font-bold text-gray-950">
      </div>
      <div class="flex gap-2 w-full sm:w-auto">
        <button type="submit" class="btn-brutal px-6 py-2 bg-brand-yellow hover:bg-yellow-400 text-black text-xs font-black uppercase">
          Apply Filter
        </button>
        <a href="{{ route('reports.index', ['from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d')]) }}" class="btn-brutal px-3 py-2 bg-gray-100 hover:bg-gray-200 text-xs font-bold uppercase" title="Reset to Today">
          Today
        </a>
      </div>
    </form>
  </div>

  <!-- SUMMARY METRICS -->
  <div class="space-y-2">
    <div class="flex justify-between items-center text-xs font-bold text-gray-500 uppercase tracking-wider">
      <span>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</span>
      <span class="font-mono text-gray-400">{{ $clubName }}</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
      <div class="card-brutal p-4 bg-white">
        <p class="text-[10px] font-black uppercase text-gray-500 tracking-wider">Total Games</p>
        <p class="text-3xl font-black text-gray-950 font-mono mt-1">{{ $totalSessions }}</p>
      </div>

      <div class="card-brutal p-4 bg-white">
        <p class="text-[10px] font-black uppercase text-gray-500 tracking-wider">Total Frames</p>
        <p class="text-3xl font-black text-gray-950 font-mono mt-1">{{ $totalRounds }}</p>
      </div>

      <div class="card-brutal p-4 bg-yellow-50 border-brand-yellow">
        <p class="text-[10px] font-black uppercase text-gray-700 tracking-wider">Total Revenue</p>
        <p class="text-2xl sm:text-3xl font-black text-gray-950 font-mono mt-1">
          {{ $currency }} {{ number_format($totalRevenue, 0) }}
        </p>
      </div>

      <div class="card-brutal p-4 bg-green-50 border-green-700">
        <p class="text-[10px] font-black uppercase text-green-800 tracking-wider">Paid Amount</p>
        <p class="text-2xl sm:text-3xl font-black text-green-950 font-mono mt-1">
          {{ $currency }} {{ number_format($paidAmount, 0) }}
        </p>
      </div>

      <div class="card-brutal p-4 bg-red-50 border-red-700 col-span-2 md:col-span-1">
        <p class="text-[10px] font-black uppercase text-red-800 tracking-wider">Unpaid Amount</p>
        <p class="text-2xl sm:text-3xl font-black text-red-950 font-mono mt-1">
          {{ $currency }} {{ number_format($unpaidAmount, 0) }}
        </p>
      </div>
    </div>
  </div>

  <!-- TABLE USAGE BREAKDOWN -->
  <div class="card-brutal p-5 bg-white space-y-3">
    <h3 class="text-xs font-black uppercase tracking-wider text-gray-950 border-b-2 border-black pb-2 flex items-center gap-2">
      <i class="fa-solid fa-table-cells"></i>
      <span>Table Utilization & Yield</span>
    </h3>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b-2 border-black bg-gray-100 text-gray-900 uppercase font-black tracking-wider">
            <th class="p-3">Table Name</th>
            <th class="p-3">Table Type</th>
            <th class="p-3">Sessions Hosted</th>
            <th class="p-3">Frames Played</th>
            <th class="p-3">Total Revenue</th>
            <th class="p-3">Share</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-gray-200 font-medium">
          @forelse($tableUsage as $tu)
            @php
              $share = $totalRevenue > 0 ? round(($tu->total_revenue / $totalRevenue) * 100, 1) : 0;
            @endphp
            <tr class="hover:bg-gray-50">
              <td class="p-3 font-black text-sm uppercase text-gray-950">{{ $tu->name }}</td>
              <td class="p-3 text-gray-600 uppercase">{{ $tu->type }}</td>
              <td class="p-3 font-mono font-bold">{{ $tu->sessions_count }}</td>
              <td class="p-3 font-mono">{{ $tu->total_rounds }}</td>
              <td class="p-3 font-mono font-black text-gray-950">{{ $currency }} {{ number_format($tu->total_revenue, 0) }}</td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <div class="w-16 bg-gray-200 h-2.5 border border-black">
                    <div class="bg-brand-yellow h-2" style="width: {{ min(100, $share) }}%"></div>
                  </div>
                  <span class="font-mono text-[10px] font-bold text-gray-600">{{ $share }}%</span>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-6 text-center text-gray-500 font-bold">No table data available.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- GAMEPLAY REVENUE BREAKDOWN -->
  <div class="card-brutal p-5 bg-white space-y-3">
    <h3 class="text-xs font-black uppercase tracking-wider text-gray-950 border-b-2 border-black pb-2 flex items-center gap-2">
      <i class="fa-solid fa-trophy"></i>
      <span>Revenue by Gameplay Mode</span>
    </h3>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      @php
        $modes = [
          'century' => ['name' => 'Century', 'icon' => '⏱️', 'badge' => 'Time-Based'],
          '6_ball' => ['name' => '6 Ball', 'icon' => '🎱', 'badge' => 'Frame-Based'],
          '10_ball' => ['name' => '10 Ball', 'icon' => '🔴', 'badge' => 'Frame-Based'],
          'one_ball' => ['name' => 'One Ball', 'icon' => '🟡', 'badge' => 'Frame-Based'],
        ];
      @endphp

      @foreach($modes as $mKey => $mInfo)
        @php
          $matched = $gameplayBreakdown->firstWhere('game_type', $mKey);
          $sessCount = $matched ? $matched->sessions_count : 0;
          $rev = $matched ? (float)$matched->total_revenue : 0;
        @endphp
        <div class="p-4 bg-gray-50 border-2 border-black space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-base">{{ $mInfo['icon'] }}</span>
            <span class="badge-brutal px-1.5 py-0.2 bg-black text-brand-yellow text-[9px]">{{ $mInfo['badge'] }}</span>
          </div>
          <p class="text-xs font-black uppercase text-gray-950 mt-1">{{ $mInfo['name'] }}</p>
          <p class="text-xl font-black text-gray-950 font-mono">{{ $currency }} {{ number_format($rev, 0) }}</p>
          <p class="text-[10px] font-bold text-gray-500 uppercase">{{ $sessCount }} session(s)</p>
        </div>
      @endforeach
    </div>
  </div>

  <!-- CASH SETTLEMENT SUMMARY -->
  <div class="card-brutal p-5 bg-yellow-50 border-2 border-black space-y-2">
    <div class="flex items-center justify-between">
      <h3 class="text-xs font-black uppercase tracking-wider text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-money-bill-wave text-green-700"></i>
        <span>Cash Register Summary</span>
      </h3>
      <span class="badge-brutal px-2 py-0.5 bg-black text-brand-yellow text-[10px]">CASH ONLY</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
      <div class="p-3 bg-white border border-black">
        <span class="text-[10px] uppercase font-bold text-gray-500 block">Total Cash Collected:</span>
        <span class="font-mono font-black text-2xl text-green-700 block mt-0.5">{{ $currency }} {{ number_format($paidAmount, 0) }}</span>
      </div>
      <div class="p-3 bg-white border border-black">
        <span class="text-[10px] uppercase font-bold text-gray-500 block">Total Cash Pending / Unpaid:</span>
        <span class="font-mono font-black text-2xl text-red-600 block mt-0.5">{{ $currency }} {{ number_format($unpaidAmount, 0) }}</span>
      </div>
    </div>
  </div>

</div>
@endsection
