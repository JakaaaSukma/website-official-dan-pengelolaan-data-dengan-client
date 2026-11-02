@extends(request()->routeIs('gik-superadmin.*') ? 'layouts.gik-superadmin' : 'layouts.gik-hrd')

@php
$rPrefix = request()->routeIs('gik-superadmin.*') ? 'gik-superadmin.' : 'gik-hrd.';
@endphp

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">{{ request()->routeIs('gik-superadmin.*') ? 'All Company Candidates' : 'Candidate Database' }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ request()->routeIs('gik-superadmin.*') ? 'Cross-tenant candidate pipeline across all company clients.' : 'Manage and review potential hires efficiently.' }}</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route($rPrefix . 'candidates.download-template') }}" class="text-sm text-gray-600 hover:text-gik-accent px-3 py-2">Download Template</a>
        <a href="{{ route($rPrefix . 'candidates.import') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border-2 border-green-600 text-green-700 rounded-lg text-sm font-semibold hover:bg-green-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Import Candidates via Excel
        </a>
        <a href="{{ route($rPrefix . 'candidates.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-semibold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            + Add Candidate
        </a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-5">
        <!-- Filters -->
        <form method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap items-center gap-6">
                <div class="text-sm font-semibold text-gray-700">Filters:</div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Role:</label>
                    <select name="role" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white min-w-[140px]">
                        <option value="all">All</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ $roleFilter === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Status:</label>
                    <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white min-w-[140px]">
                        <option value="all">All</option>
                        <option value="new" {{ $statusFilter === 'new' ? 'selected' : '' }}>New</option>
                        <option value="interviewing" {{ $statusFilter === 'interviewing' ? 'selected' : '' }}>Interviewing</option>
                        <option value="offer_sent" {{ $statusFilter === 'offer_sent' ? 'selected' : '' }}>Offer Sent</option>
                        <option value="hired" {{ $statusFilter === 'hired' ? 'selected' : '' }}>Hired</option>
                        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="passive" {{ $statusFilter === 'passive' ? 'selected' : '' }}>Passive</option>
                    </select>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search candidate..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 min-w-[220px]">
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-medium">Filter</button>
                </div>
            </div>
        </form>

        <!-- Candidate Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($candidates as $cand)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-gray-200 transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-300 to-indigo-400 text-white flex items-center justify-center text-lg font-bold shadow-sm flex-shrink-0">
                                {{ $cand->initials }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-base">{{ $cand->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $cand->job_title }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap {{ $cand->status_badge_class }}">
                            {{ strtoupper(str_replace('_', ' ', $cand->status)) }}
                        </span>
                    </div>

                    <div class="space-y-1.5 mb-4 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ number_format($cand->years_experience, 1) }} Years Exp</span>
                        </div>
                        @if($cand->education)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                <span>{{ \Illuminate\Support\Str::limit($cand->education, 40) }}</span>
                            </div>
                        @endif
                        @if($cand->skills)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span>{{ \Illuminate\Support\Str::limit($cand->skills, 40) }}</span>
                            </div>
                        @endif
                        @if($cand->department)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $cand->department }}</span>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route($rPrefix . 'candidates.show', $cand->id) }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 border-2 border-gik-primary text-gik-primary rounded-xl text-sm font-bold hover:bg-gik-primary hover:text-white transition">
                        View CV
                    </a>
                </div>
            @empty
                <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Belum ada kandidat</h3>
                    <p class="text-sm text-gray-500 mb-4">Gunakan "Import Candidates via Excel" atau tambahkan kandidat baru.</p>
                    <a href="{{ route($rPrefix . 'candidates.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-lg text-sm font-semibold">
                        Tambah Kandidat Pertama
                    </a>
                </div>
            @endforelse
        </div>

        @if($candidates->hasPages())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-3 flex items-center justify-between">
                <div class="text-sm text-gray-500">Showing {{ $candidates->firstItem() }}–{{ $candidates->lastItem() }} of {{ $candidates->total() }} candidates</div>
                <div>
                    {{ $candidates->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar Right: Pusat Request -->
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24">
            <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gik-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <h2 class="text-xl font-bold text-gray-800">Pusat Request</h2>
            </div>
            <div class="space-y-3.5">
                <a href="{{ route($rPrefix . 'requests.create', 'add_contract') }}" class="group block p-4 border border-gray-200 rounded-xl hover:border-green-200 hover:bg-green-50/50 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl bg-green-100 text-green-700 flex items-center justify-center group-hover:scale-105 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">Add New Contract</div>
                                <div class="text-xs text-gray-500 mt-0.5 leading-relaxed">Initiate onboarding for a new hire.</div>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-green-600 group-hover:translate-x-0.5 transition mt-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                <a href="{{ route($rPrefix . 'requests.create', 'extend_contract') }}" class="group block p-4 border border-gray-200 rounded-xl hover:border-red-200 hover:bg-red-50/50 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center group-hover:scale-105 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">Extend Contract</div>
                                <div class="text-xs text-gray-500 mt-0.5 leading-relaxed">Renew an existing employee agreement.</div>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-gik-accent group-hover:translate-x-0.5 transition mt-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                <a href="{{ route($rPrefix . 'requests.create', 'off_boarding') }}" class="group block p-4 border border-gray-200 rounded-xl hover:border-red-200 hover:bg-red-50/50 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl bg-red-100 text-red-700 flex items-center justify-center group-hover:scale-105 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">Off-boarding</div>
                                <div class="text-xs text-gray-500 mt-0.5 leading-relaxed">Process employee departure.</div>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition mt-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
