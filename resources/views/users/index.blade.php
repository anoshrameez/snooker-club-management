@extends('layouts.app')

@section('title', 'Staff & User Access')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b-2 border-black">
    <div>
      <h1 class="text-2xl font-black uppercase tracking-tight text-gray-950 flex items-center gap-2">
        <i class="fa-solid fa-user-shield text-brand-yellow bg-black p-1 text-sm"></i>
        <span>Staff & Access Management</span>
      </h1>
      <p class="text-xs font-bold text-gray-600 uppercase tracking-widest mt-1">
        Manage Administrator & Reception Staff Usernames & Passwords
      </p>
    </div>
    <button onclick="document.getElementById('add-user-box').classList.toggle('hidden')" class="btn-brutal px-4 py-2 bg-brand-yellow hover:bg-yellow-400 text-black text-xs uppercase font-black flex items-center gap-1.5 self-start sm:self-auto">
      <i class="fa-solid fa-user-plus"></i>
      <span>+ Add New User</span>
    </button>
  </div>

  <!-- Add User Form (Collapsible) -->
  <div id="add-user-box" class="hidden card-brutal p-6 bg-yellow-50 border-2 border-black space-y-4">
    <div class="flex items-center justify-between border-b-2 border-black pb-2">
      <h3 class="text-sm font-black uppercase tracking-wider text-gray-950 flex items-center gap-1.5">
        <i class="fa-solid fa-user-plus"></i>
        <span>Create Login Account (Username + Password)</span>
      </h3>
      <button type="button" onclick="document.getElementById('add-user-box').classList.add('hidden')" class="text-xs font-black uppercase hover:text-red-600">✕ Close</button>
    </div>

    <form action="{{ route('users.store', [], false) }}" method="POST" class="space-y-4">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div>
          <label class="block text-[10px] font-black uppercase text-gray-700 mb-1">Full Name *</label>
          <input type="text" name="name" required placeholder="e.g. Asad Receptionist" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
        </div>
        <div>
          <label class="block text-[10px] font-black uppercase text-gray-700 mb-1">Username * (Login ID)</label>
          <input type="text" name="username" required placeholder="e.g. staff2" class="w-full input-brutal px-3 py-2 text-xs font-mono font-bold text-gray-950">
        </div>
        <div>
          <label class="block text-[10px] font-black uppercase text-gray-700 mb-1">Password *</label>
          <input type="password" name="password" required placeholder="Min 4 characters" class="w-full input-brutal px-3 py-2 text-xs text-gray-950">
        </div>
        <div>
          <label class="block text-[10px] font-black uppercase text-gray-700 mb-1">Role *</label>
          <select name="role" class="w-full input-brutal px-3 py-2 text-xs font-bold text-gray-950">
            <option value="staff">Staff (Reception Operations)</option>
            <option value="admin">Administrator (Full Access)</option>
          </select>
        </div>
      </div>
      <div class="flex justify-end gap-2 pt-2 border-t border-gray-300">
        <button type="button" onclick="document.getElementById('add-user-box').classList.add('hidden')" class="btn-brutal px-4 py-1.5 bg-white text-xs font-bold">
          Cancel
        </button>
        <button type="submit" class="btn-brutal px-6 py-1.5 bg-black text-white text-xs font-black uppercase">
          Create Account
        </button>
      </div>
    </form>
  </div>

  <!-- Users Table -->
  <div class="card-brutal bg-white overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b-2 border-black bg-gray-100 text-gray-900 uppercase font-black tracking-wider">
            <th class="p-3.5">Name</th>
            <th class="p-3.5">Username (Login ID)</th>
            <th class="p-3.5">Role</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5">Created</th>
            <th class="p-3.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-gray-200 font-medium">
          @foreach($users as $u)
            <tr class="hover:bg-gray-50">
              <td class="p-3.5 font-black text-sm text-gray-950">
                {{ $u->name }}
                @if($u->id === auth()->id())
                  <span class="text-[10px] font-bold text-gray-400 font-mono">(You)</span>
                @endif
              </td>
              <td class="p-3.5 font-mono font-bold text-gray-900 text-sm">
                {{ $u->username ?? 'admin' }}
              </td>
              <td class="p-3.5">
                <span class="badge-brutal px-2.5 py-0.5 text-[10px] {{ $u->isAdmin() ? 'bg-brand-yellow text-black border-black' : 'bg-gray-100 text-gray-800' }}">
                  {{ strtoupper($u->role) }}
                </span>
              </td>
              <td class="p-3.5">
                <span class="badge-brutal px-2 py-0.5 text-[10px] {{ $u->is_active ? 'bg-green-100 text-green-950 border-green-700' : 'bg-red-100 text-red-950 border-red-700' }}">
                  {{ $u->is_active ? 'ACTIVE' : 'DEACTIVATED' }}
                </span>
              </td>
              <td class="p-3.5 font-mono text-gray-500">
                {{ $u->created_at->format('d M Y') }}
              </td>
              <td class="p-3.5 text-right space-x-1.5 whitespace-nowrap">
                <!-- Change Password Button -->
                <button type="button" onclick="document.getElementById('password-modal-{{ $u->id }}').classList.toggle('hidden')" class="btn-brutal px-2.5 py-1 bg-white hover:bg-gray-100 text-[10px] font-bold uppercase" title="Reset Password">
                  <i class="fa-solid fa-key"></i> Password
                </button>

                <!-- Status Toggle -->
                @if($u->id !== auth()->id())
                  <form action="{{ route('users.toggle', $u->id, false) }}" method="POST" class="inline" onsubmit="return confirm('Toggle status for user {{ $u->username }}?')">
                    @csrf
                    <button type="submit" class="btn-brutal px-2.5 py-1 text-[10px] font-bold uppercase {{ $u->is_active ? 'bg-red-100 hover:bg-red-200 text-red-900' : 'bg-green-100 hover:bg-green-200 text-green-950' }}">
                      {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                  </form>
                @endif

                <!-- Inline Password Reset Box -->
                <div id="password-modal-{{ $u->id }}" class="hidden text-left mt-2 p-3 bg-yellow-50 border-2 border-black space-y-2">
                  <form action="{{ route('users.password', $u->id, false) }}" method="POST" class="flex gap-2 items-center">
                    @csrf
                    @method('PUT')
                    <input type="password" name="password" required placeholder="New password for {{ $u->username }}" class="input-brutal px-2 py-1 text-xs font-mono">
                    <button type="submit" class="btn-brutal px-3 py-1 bg-black text-white text-[10px] font-black uppercase">
                      Update
                    </button>
                    <button type="button" onclick="document.getElementById('password-modal-{{ $u->id }}').classList.add('hidden')" class="btn-brutal px-2 py-1 bg-white text-[10px] font-bold">
                      ✕
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
