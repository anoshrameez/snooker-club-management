<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="robots" content="noindex, nofollow">
  <title>@yield('title', 'Snooker Club Operations') — {{ \App\Models\Setting::get('club_name', 'CueMaster Club') }}</title>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              yellow: '#FACC15', // Neo-Brutalist primary accent
              green: '#22C55E',
              red: '#EF4444',
              blue: '#38BDF8',
              bg: '#F8F9FA',
              dark: '#111827'
            }
          },
          fontFamily: {
            sans: ['Space Grotesk', 'Inter', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace']
          },
          boxShadow: {
            'brutal': '4px 4px 0px 0px #000000',
            'brutal-sm': '2px 2px 0px 0px #000000',
            'brutal-lg': '6px 6px 0px 0px #000000',
            'brutal-active': '1px 1px 0px 0px #000000',
          }
        }
      }
    }
  </script>

  <!-- Google Fonts: Space Grotesk -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    body {
      font-family: 'Space Grotesk', sans-serif;
      background-color: #F8F9FA;
      color: #111827;
    }
    .font-mono {
      font-family: 'JetBrains Mono', monospace;
    }
    /* Neo-Brutalist utility classes */
    .btn-brutal {
      border: 2px solid #000;
      box-shadow: 4px 4px 0px 0px #000;
      transition: all 0.1s ease;
      font-weight: 700;
    }
    .btn-brutal:hover {
      box-shadow: 2px 2px 0px 0px #000;
      transform: translate(2px, 2px);
    }
    .btn-brutal:active {
      box-shadow: 0px 0px 0px 0px #000;
      transform: translate(4px, 4px);
    }
    .card-brutal {
      background: #FFFFFF;
      border: 2px solid #000;
      box-shadow: 4px 4px 0px 0px #000;
    }
    .badge-brutal {
      border: 2px solid #000;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
    }
    .input-brutal {
      border: 2px solid #000;
      background: #FFFFFF;
      font-weight: 500;
      transition: all 0.15s;
    }
    .input-brutal:focus {
      outline: none;
      box-shadow: 3px 3px 0px 0px #000;
    }
  </style>
  @stack('styles')
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased selection:bg-yellow-300 selection:text-black">

  <!-- MOBILE TOP BAR -->
  <header class="md:hidden bg-white border-b-2 border-black p-4 flex items-center justify-between sticky top-0 z-40">
    <div class="flex items-center space-x-2">
      <div class="w-8 h-8 bg-brand-yellow border-2 border-black flex items-center justify-center font-bold text-sm shadow-brutal-sm">
        🎱
      </div>
      <span class="font-bold tracking-tight text-base uppercase">{{ \App\Models\Setting::get('club_name', 'CueMaster') }}</span>
    </div>
    <div class="flex items-center space-x-2">
      <a href="{{ route('sessions.create') }}" class="btn-brutal px-3 py-1 bg-brand-yellow text-xs flex items-center gap-1">
        <i class="fa-solid fa-plus text-[10px]"></i>
        <span>NEW</span>
      </a>
      <button onclick="document.getElementById('sidebar').classList.toggle('hidden')" class="p-2 border-2 border-black bg-white hover:bg-gray-100">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </header>

  <!-- SIDEBAR NAVIGATION -->
  <aside id="sidebar" class="hidden md:flex flex-col w-full md:w-64 bg-white border-r-2 border-black shrink-0 min-h-screen sticky top-0 z-30 justify-between">
    <div>
      <!-- Brand Logo / Header -->
      <div class="p-5 border-b-2 border-black bg-white">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
          <div class="w-10 h-10 bg-brand-yellow border-2 border-black flex items-center justify-center text-lg font-black shadow-brutal-sm group-hover:translate-x-0.5 group-hover:translate-y-0.5 transition">
            🎱
          </div>
          <div>
            <h1 class="font-black text-lg tracking-tight uppercase leading-tight">{{ \App\Models\Setting::get('club_name', 'CueMaster') }}</h1>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Club Management</p>
          </div>
        </a>
      </div>

      <!-- Action Button: + NEW SESSION -->
      <div class="p-4 border-b-2 border-black bg-gray-50">
        <a href="{{ route('sessions.create') }}" class="w-full btn-brutal py-3 px-4 bg-brand-yellow hover:bg-yellow-400 text-black flex items-center justify-center gap-2 text-sm uppercase tracking-wide">
          <i class="fa-solid fa-circle-plus text-base"></i>
          <span>+ NEW SESSION</span>
        </a>
      </div>

      <!-- Main Navigation Menu -->
      <nav class="p-3 space-y-1.5 text-sm font-bold">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('dashboard') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
          <i class="fa-solid fa-chart-pie w-4 text-center"></i>
          <span>Dashboard</span>
        </a>

        <a href="{{ route('sessions.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('sessions.create') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
          <i class="fa-solid fa-play w-4 text-center"></i>
          <span>New Session</span>
        </a>

        <a href="{{ route('sessions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('sessions.index') || request()->routeIs('sessions.show') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
          <i class="fa-solid fa-clock-rotate-left w-4 text-center"></i>
          <span>Sessions History</span>
        </a>

        <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('customers.*') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
          <i class="fa-solid fa-users w-4 text-center"></i>
          <span>Customers</span>
        </a>

        <a href="{{ route('tables.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('tables.*') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
          <i class="fa-solid fa-border-all w-4 text-center"></i>
          <span>Tables</span>
        </a>

        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('reports.*') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
          <i class="fa-solid fa-file-invoice-dollar w-4 text-center"></i>
          <span>Reports</span>
        </a>

        @if(auth()->user() && auth()->user()->isAdmin())
          <div class="pt-3 pb-1 px-3">
            <span class="text-[10px] uppercase font-black tracking-widest text-gray-400">Admin Controls</span>
          </div>

          <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('settings.*') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
            <i class="fa-solid fa-sliders w-4 text-center"></i>
            <span>Settings</span>
          </a>

          <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none border-2 {{ request()->routeIs('users.*') ? 'bg-black text-white border-black shadow-brutal-sm' : 'border-transparent text-gray-800 hover:border-black hover:bg-gray-100' }}">
            <i class="fa-solid fa-user-shield w-4 text-center"></i>
            <span>Staff & Users</span>
          </a>
        @endif
      </nav>
    </div>

    <!-- User Profile & Logout -->
    <div class="p-4 border-t-2 border-black bg-gray-50">
      <div class="flex items-center justify-between mb-3">
        <div>
          <p class="font-bold text-sm text-gray-900 leading-tight">{{ auth()->user()?->name ?? 'Guest User' }}</p>
          <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider border border-black {{ auth()->user()?->isAdmin() ? 'bg-brand-yellow text-black' : 'bg-gray-200 text-gray-800' }}">
            {{ auth()->user()?->role ?? 'Staff' }}
          </span>
        </div>
      </div>
      <form action="{{ route('logout', [], false) }}" method="POST">
        @csrf
        <button type="submit" class="w-full btn-brutal py-1.5 px-3 bg-white hover:bg-red-50 text-red-600 text-xs flex items-center justify-center gap-1.5 uppercase">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN WORKSPACE CONTENT -->
  <main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- FLASH MESSAGES -->
    @if(session('success'))
      <div class="mb-6 card-brutal bg-green-100 p-4 flex items-center justify-between border-green-600 border-2 shadow-brutal">
        <div class="flex items-center gap-3 text-green-950 font-bold text-sm">
          <i class="fa-solid fa-circle-check text-green-700 text-lg"></i>
          <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-green-800 hover:text-black font-black text-sm">✕</button>
      </div>
    @endif

    @if(session('error'))
      <div class="mb-6 card-brutal bg-red-100 p-4 flex items-center justify-between border-red-600 border-2 shadow-brutal">
        <div class="flex items-center gap-3 text-red-950 font-bold text-sm">
          <i class="fa-solid fa-triangle-exclamation text-red-700 text-lg"></i>
          <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-800 hover:text-black font-black text-sm">✕</button>
      </div>
    @endif

    @if(session('info'))
      <div class="mb-6 card-brutal bg-blue-100 p-4 flex items-center justify-between border-blue-600 border-2 shadow-brutal">
        <div class="flex items-center gap-3 text-blue-950 font-bold text-sm">
          <i class="fa-solid fa-circle-info text-blue-700 text-lg"></i>
          <span>{{ session('info') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-blue-800 hover:text-black font-black text-sm">✕</button>
      </div>
    @endif

    @if(isset($errors) && $errors->any())
      <div class="mb-6 card-brutal bg-red-50 p-4 border-red-600 border-2 shadow-brutal">
        <div class="flex items-center gap-2 text-red-900 font-bold text-sm mb-2">
          <i class="fa-solid fa-circle-xmark text-red-600"></i>
          <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside text-xs text-red-800 font-semibold space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- YIELD VIEW CONTENT -->
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
