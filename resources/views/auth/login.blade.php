<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Login — {{ \App\Models\Setting::get('club_name', 'CueMaster Club') }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      font-family: 'Space Grotesk', sans-serif;
      background-color: #F4F4F0;
    }
    .card-brutal {
      background: #FFFFFF;
      border: 3px solid #000;
      box-shadow: 6px 6px 0px 0px #000;
    }
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
    .input-brutal {
      border: 2px solid #000;
      background: #FFFFFF;
      font-weight: 500;
    }
    .input-brutal:focus {
      outline: none;
      box-shadow: 3px 3px 0px 0px #000;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-[#F4F4F0]">

  <div class="max-w-md w-full">
    <!-- Header Badge -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-[#FACC15] border-3 border-black shadow-[4px_4px_0px_0px_#000] text-3xl mb-3">
        🎱
      </div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950">{{ \App\Models\Setting::get('club_name', 'CueMaster Snooker Club') }}</h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">Reception Counter Access</p>
    </div>

    <!-- Login Card -->
    <div class="card-brutal p-6 sm:p-8">
      <h2 class="text-lg font-black uppercase tracking-tight mb-5 border-b-2 border-black pb-3 flex items-center justify-between">
        <span>Authorized Access</span>
        <span class="text-xs px-2 py-0.5 bg-yellow-300 border border-black font-mono">STAFF / ADMIN</span>
      </h2>

      @if($errors->any())
        <div class="mb-5 p-3 bg-red-100 border-2 border-red-600 text-xs text-red-900 font-bold">
          <i class="fa-solid fa-circle-exclamation mr-1 text-red-600"></i>
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('login.post', [], false) }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label class="block text-xs font-black uppercase tracking-wider mb-1.5 text-gray-900">Username</label>
          <input 
            type="text" 
            name="username" 
            id="username" 
            required 
            autofocus
            value="{{ old('username') }}" 
            class="w-full input-brutal px-4 py-2.5 text-sm text-gray-900 font-bold" 
            placeholder="Enter username"
          >
        </div>

        <div>
          <label class="block text-xs font-black uppercase tracking-wider mb-1.5 text-gray-900">Password</label>
          <input 
            type="password" 
            name="password" 
            id="password" 
            required 
            class="w-full input-brutal px-4 py-2.5 text-sm text-gray-900" 
            placeholder="Enter password"
          >
        </div>

        <div class="flex items-center justify-between text-xs pt-1">
          <label class="flex items-center gap-2 cursor-pointer font-bold select-none">
            <input type="checkbox" name="remember" class="w-4 h-4 border-2 border-black rounded-none text-black focus:ring-0">
            <span>Remember me on this counter</span>
          </label>
        </div>

        <button type="submit" class="w-full btn-brutal py-3 bg-[#FACC15] hover:bg-yellow-400 text-black text-sm uppercase tracking-wider flex items-center justify-center gap-2 mt-2 font-black">
          <i class="fa-solid fa-lock text-xs"></i>
          <span>Enter Dashboard</span>
        </button>
      </form>
    </div>
  </div>

</body>
</html>
