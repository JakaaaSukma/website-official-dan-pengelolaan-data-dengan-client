@extends(request()->routeIs('gik-superadmin.*') ? 'layouts.gik-superadmin' : 'layouts.gik-hrd')

@php
$isSA = request()->routeIs('gik-superadmin.*');
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
  <div>
    <div class="text-[11px] font-bold uppercase tracking-widest text-gik-accent mb-1">
      {{ $isSA ? 'SUPER ADMIN • ALL TENANT AUDIT' : 'HR COMPANY • ACTIVITY LOG' }}
    </div>
    <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">{{ $pageTitle ?? 'Log Aktivitas Sistem' }}</h1>
    <p class="text-gray-500 text-sm mt-1">
      {{ $isSA ? 'Rekam jejak lengkap SEMUA aksi pengguna (SA + HRD Company) secara realtime, immutable & tidak bisa dihapus user.' : 'Rekam jejak semua aksi Anda dan aktivitas perusahaan Anda.' }}
    </p>
  </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">Aktivitas Hari Ini</div>
    <div class="text-2xl font-extrabold text-gray-800">{{ $stats['totalToday'] ?? 0 }}</div>
    <div class="text-[11px] text-gray-500 mt-1 font-semibold">{{ now()->format('d M Y') }}</div>
  </div>
  <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5">
    <div class="text-[11px] font-bold uppercase tracking-wider text-amber-600 mb-2">Severity Warning</div>
    <div class="text-2xl font-extrabold text-amber-700">{{ $stats['warning'] ?? 0 }}</div>
    <div class="text-[11px] text-amber-500 mt-1 font-semibold">perlu perhatian</div>
  </div>
  <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5">
    <div class="text-[11px] font-bold uppercase tracking-wider text-red-600 mb-2">Severity CRITICAL</div>
    <div class="text-2xl font-extrabold text-red-700">{{ $stats['critical'] ?? 0 }}</div>
    <div class="text-[11px] text-red-500 mt-1 font-semibold">aksi sensitif</div>
  </div>
  <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5">
    <div class="text-[11px] font-bold uppercase tracking-wider text-gik-accent mb-2">{{ $isSA ? 'User Aktif Hari Ini' : 'Akun Anda' }}</div>
    <div class="text-2xl font-extrabold text-blue-700">{{ $stats['uniqueUsers'] ?? 1 }}</div>
    <div class="text-[11px] text-blue-500 mt-1 font-semibold">{{ $isSA ? 'orang login hari ini' : Auth::user()->email }}</div>
  </div>
</div>

<form method="GET" class="mb-5 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-wrap items-center gap-3">
  @php
  $curRoute = request()->routeIs('gik-superadmin.*') ? 'gik-superadmin.activity-logs' : 'gik-hrd.activity-logs';
  @endphp
  <div class="relative flex-1 min-w-[240px]">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action name, entity, IP, user name/email..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent text-sm">
  </div>
  <select name="severity" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
    <option value="">Semua Severity</option>
    <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>ℹ️ Info</option>
    <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
    <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>🚨 Critical</option>
  </select>
  <button type="submit" class="px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-xs font-bold transition shadow-sm">Filter</button>
  @if(request('search') || request('severity'))
    <a href="{{ route($curRoute) }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-50 transition">Reset</a>
  @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider sticky top-0 z-10">
        <tr>
          <th class="px-5 py-3.5 text-left w-16">Severity</th>
          <th class="px-5 py-3.5 text-left">Waktu</th>
          @if($isSA)<th class="px-5 py-3.5 text-left">User / Pelaku</th>@endif
          <th class="px-5 py-3.5 text-left">Aksi</th>
          <th class="px-5 py-3.5 text-left">Entity</th>
          <th class="px-5 py-3.5 text-left">IP Address</th>
          <th class="px-5 py-3.5 text-left max-w-xs">URL</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($logs as $log)
          @php
          $actHuman = str_replace('_', ' ', $log->action);
          @endphp
          <tr class="hover:bg-red-50/60/30 transition">
            <td class="px-5 py-4">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[10px] font-extrabold {{ $log->severity_badge_class }}">
                {{ strtoupper(substr($log->severity, 0, 2)) }}
              </span>
            </td>
            <td class="px-5 py-4 whitespace-nowrap">
              <div class="font-bold text-gray-800 text-xs">{{ $log->created_at?->format('d M Y') }}</div>
              <div class="text-[11px] text-gray-500 font-mono">{{ $log->created_at?->format('H:i:s') }}</div>
            </td>
            @if($isSA)
            <td class="px-5 py-4">
              @if($log->user)
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-[10px] font-extrabold">
                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                  </div>
                  <div>
                    <div class="font-bold text-gray-800 text-xs">{{ $log->user->name }}</div>
                    <div class="text-[10px] text-gray-500">{{ optional($log->user->company)->company_name ? ' • ' . Str::limit(optional($log->user->company)->company_name, 18) : '' }} • {{ $log->user->role }}</div>
                  </div>
                </div>
              @else
                <span class="text-[11px] text-gray-400 italic">System</span>
              @endif
            </td>
            @endif
            <td class="px-5 py-4">
              <div class="font-bold text-gik-primary-dark text-xs capitalize leading-tight">{{ $actHuman }}</div>
            </td>
            <td class="px-5 py-4">
              <div class="text-[11px] font-bold text-gray-600">{{ $log->entity_type }}</div>
              <div class="text-[10px] text-gray-500 font-mono">ID #{{ $log->entity_id ?? '—' }}</div>
            </td>
            <td class="px-5 py-4">
              <span class="font-mono text-[11px] px-2 py-1 rounded-md bg-gray-100 text-gray-700 font-bold">{{ $log->ip_address ?? '—' }}</span>
            </td>
            <td class="px-5 py-4">
              <div class="text-[11px] text-gray-500 truncate max-w-xs" title="{{ $log->url }}">{{ Str::limit($log->url ?? '—', 50) }}</div>
            </td>
          </tr>
        @empty
          <tr><td colspan="{{ $isSA ? 7 : 6 }}" class="px-5 py-16 text-center text-gray-500 text-sm font-semibold">
            Belum ada aktivitas yang terekam.
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 flex items-center justify-between rounded-b-2xl">
      <span>Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} log entries</span>
      {{ $logs->links() }}
    </div>
  @endif
</div>
@endsection
