@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Talent Management</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Talent Database & Matching</h1>
        <p class="text-gray-500 text-sm mt-1">Global talent pool. Match candidates to client hiring needs.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-superadmin.talents.import') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-green-600 text-green-700 bg-white hover:bg-green-50 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import Excel
        </a>
        <button onclick="document.getElementById('addTalentModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            + Add Candidate
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT 2 cols: Talent Table -->
    <div class="lg:col-span-2 space-y-5">
        <!-- TABS -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex border-b border-gray-100 px-3">
                <a href="{{ request()->fullUrlWithQuery(['tab'=>null]) }}" class="px-5 py-4 font-bold text-sm border-b-2 transition
                    @if($tab==='ready') border-gik-primary text-gik-primary
                    @else border-transparent text-gray-500 hover:text-gray-800 @endif">
                    Ready to Hire <span class="ml-1.5 px-2 py-0.5 rounded-full bg-gik-accent-light text-gik-accent text-xs font-extrabold">{{ $counts['ready'] ?? 0 }}</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['tab'=>'in_process']) }}" class="px-5 py-4 font-bold text-sm border-b-2 transition
                    @if($tab==='in_process') border-gik-primary text-gik-primary
                    @else border-transparent text-gray-500 hover:text-gray-800 @endif">
                    In Process <span class="ml-1.5 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-extrabold">{{ $counts['in_process'] ?? 0 }}</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['tab'=>'passive']) }}" class="px-5 py-4 font-bold text-sm border-b-2 transition
                    @if($tab==='passive') border-gik-primary text-gik-primary
                    @else border-transparent text-gray-500 hover:text-gray-800 @endif">
                    Passive <span class="ml-1.5 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-extrabold">{{ $counts['passive'] ?? 0 }}</span>
                </a>
            </div>

            <!-- Search bar -->
            <div class="px-5 py-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" placeholder="Search candidates by name, role, or skill..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm">
                </div>
                <select class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option>All Roles</option>
                    <option>Software Engineer</option>
                    <option>UI/UX Designer</option>
                    <option>Product Manager</option>
                    <option>Data Engineer</option>
                    <option>Marketing</option>
                </select>
                <a href="{{ route('gik-superadmin.talents.template') }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Template
                </a>
            </div>

            <!-- Talent Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Candidate</th>
                            <th class="px-5 py-3.5 text-left">Role Match</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-left w-44">Score</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($talents as $t)
                            @php
                                $s = $t->match_score ?: 75;
                                $barCls = $s >= 90 ? 'bg-green-500' : ($s >= 80 ? 'bg-emerald-500' : ($s >= 70 ? 'bg-amber-500' : 'bg-orange-500'));
                            @endphp
                            <tr class="hover:bg-red-50/60/30 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                                            {{ $t->initials }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 leading-tight">{{ $t->full_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $t->current_job_title ?? $t->target_role }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1.5 max-w-[220px]">
                                        @foreach($t->skill_tags_array as $tag)
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-gray-100 text-gray-700">{{ $tag }}</span>
                                        @endforeach
                                        @if(empty($t->skill_tags_array))
                                            <span class="text-xs text-gray-500 italic">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold w-fit
                                            @if($t->availability_status === 'available') bg-green-100 text-green-700
                                            @elseif($t->availability_status === 'notice_period') bg-amber-100 text-amber-700
                                            @elseif($t->availability_status === 'employed') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-600 @endif
                                        ">{{ strtoupper(str_replace('_', ' ', $t->availability_status ?? 'available')) }}</span>
                                        <span class="text-[10px] text-gray-500 font-semibold">Exp: {{ $t->years_experience ?? 0 }} years • Rp {{ number_format(($t->expected_salary ?? 8000000)/1000000, 1) }}M</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 gik-progress gik-progress-thick"><div class="gik-progress-bar {{ $barCls }}" style="width: {{ $s }}%"></div></div>
                                        <span class="text-sm font-extrabold text-gray-800 w-10 text-right">{{ $s }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap space-x-2">
                                    <button class="text-xs font-bold text-gik-accent hover:text-gik-primary px-2 py-1">Match</button>
                                    <span class="text-gray-300 text-xs">|</span>
                                    <a href="#" class="text-xs font-bold text-gik-accent hover:text-gik-primary px-2 py-1">CV</a>
                                    <span class="text-gray-300 text-xs">|</span>
                                    <form method="POST" action="{{ route('gik-superadmin.talents.destroy', $t) }}" onsubmit="return confirm('Delete talent?')" class="inline">@csrf @method('DELETE')
                                        <button class="text-xs font-bold text-red-600 hover:text-red-800 px-2 py-1">Del</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-16 text-center">
                                <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                                <h3 class="font-bold text-gray-700 mb-1">No candidates in this tab yet.</h3>
                                <p class="text-sm text-gray-500">Add new candidates manually or import via Excel.</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($talents->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">{{ $talents->links() }}</div>
            @endif
        </div>
    </div>

    <!-- RIGHT 1 col: Active Client Needs -->
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-extrabold text-gray-800 leading-tight">Active Client Needs</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Hiring requirements across tenants.</p>
                </div>
                <button onclick="document.getElementById('addNeedModal').classList.remove('hidden')" class="w-8 h-8 rounded-lg bg-gik-primary hover:bg-gik-primary-dark text-white inline-flex items-center justify-center shadow-sm transition" title="Add need">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>

            <div class="space-y-3.5">
                @forelse($clientNeeds as $need)
                    <div class="rounded-xl border border-gray-100 p-4 hover:border-gik-accent/30 transition relative overflow-hidden">
                        @if($need->urgency === 'urgent')<div class="absolute top-0 left-0 w-1.5 h-full bg-red-500"></div>@elseif($need->urgency === 'open')<div class="absolute top-0 left-0 w-1.5 h-full bg-red-50/600"></div>@endif
                        <div class="pl-2">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <div class="text-xs text-gray-500 font-semibold mb-0.5">{{ optional($need->company)->company_name ?? 'Unknown Tenant' }}</div>
                                    <div class="font-extrabold text-gray-800 leading-tight">{{ $need->job_title }}</div>
                                </div>
                                <span class="inline-flex items-center text-[10px] font-extrabold px-2 py-0.5 rounded-full
                                    @if($need->urgency === 'urgent') bg-red-100 text-red-700
                                    @elseif($need->urgency === 'low') bg-gray-100 text-gray-600
                                    @else bg-blue-100 text-blue-700 @endif
                                ">
                                    {{ strtoupper($need->urgency) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($need->skill_tags_array as $s)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-red-100 text-gik-primary-dark">{{ $s }}</span>
                                @endforeach
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3 text-xs text-gray-500 font-semibold">
                                    <span>{{ $need->quota ?? 1 }} position(s)</span>
                                    <span>•</span>
                                    @if($need->deadline)
                                        <span>Due {{ \Carbon\Carbon::parse($need->deadline)->diffForHumans() }}</span>
                                    @endif
                                </div>
                                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($need->status === 'open') bg-green-100 text-green-700
                                    @elseif($need->status === 'in_progress') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-600 @endif
                                ">{{ strtoupper(str_replace('_', ' ', $need->status)) }}</span>
                            </div>

                            @if($need->valid_proof_path)
                            <div class="mb-3">
                                <a href="{{ Storage::url($need->valid_proof_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                    📄 Lihat Bukti Valid
                                </a>
                            </div>
                            @endif

                            <!-- Matched candidates mini -->
                            @php
                                $matches = $need->talentMatches ?? collect();
                                $matchesCount = $matches->count();
                            @endphp
                            @if($matchesCount > 0)
                                <div class="flex items-center justify-between py-2 px-2.5 rounded-lg bg-gray-50/70 mb-3">
                                    <div class="flex -space-x-1.5">
                                        @foreach($matches->take(4) as $m)
                                            <div class="w-6 h-6 rounded-full ring-2 ring-white flex items-center justify-center text-[9px] font-extrabold text-white
                                                bg-gradient-to-br from-{{ ['indigo','purple','rose','blue'][$loop->index % 4] }}-500 to-{{ ['indigo','purple','rose','blue'][$loop->index % 4] }}-700
                                            ">
                                                {{ strtoupper(substr(optional($m->talent)->first_name ?? 'T', 0, 1)) }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-[10px] text-gray-600 font-bold">
                                        {{ $matchesCount }} match{{ $matchesCount > 1 ? 'es' : '' }} • Top {{ $matchesCount > 0 ? optional($matches->first())->match_score . '%' : '—' }}
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('gik-superadmin.client-needs.match', $need) }}" class="w-full">
                                @csrf
                                <button class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 border-2 border-gik-primary text-gik-primary hover:bg-gik-primary hover:text-white rounded-lg text-xs font-bold transition shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Run Match Engine
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center border border-dashed border-gray-200 rounded-xl">
                        <div class="text-xs font-bold text-gray-500 mb-2">No Active Client Needs</div>
                        <button onclick="document.getElementById('addNeedModal').classList.remove('hidden')" class="px-3 py-1.5 text-xs font-bold text-gik-accent hover:underline">+ Create the first hiring need</button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Talent Call Requests from HR Company -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-extrabold text-gray-800 leading-tight flex items-center gap-2">
                        📞 Talent Call Requests
                        @if($talentCallRequests->where('status','pending')->count() > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-100 text-red-700">{{ $talentCallRequests->where('status','pending')->count() }} PENDING</span>
                        @endif
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Permintaan Test / Interview dari HR Company.</p>
                </div>
            </div>
            <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                @forelse($talentCallRequests as $cr)
                    <div class="rounded-xl border border-gray-100 p-4 hover:border-purple-200 hover:bg-purple-50/20 transition relative overflow-hidden">
                        @if($cr->status==='pending')<div class="absolute top-0 left-0 w-1 h-full bg-amber-400"></div>@elseif($cr->status==='scheduled')<div class="absolute top-0 left-0 w-1 h-full bg-red-50/600"></div>@elseif($cr->status==='completed')<div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>@endif
                        <div class="pl-2">
                            <div class="flex flex-wrap items-start justify-between gap-2 mb-2.5">
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wide text-gray-400 mb-0.5">{{ optional($cr->company)->company_name ?? 'Unknown Tenant' }} • {{ optional($cr->requestedBy)->name ?? 'HR Company' }}</div>
                                    <div class="font-extrabold text-gray-800 leading-tight">{{ optional($cr->talent)->full_name ?? 'Talent #'.$cr->talent_id }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ optional($cr->talent)->target_role ?? '—' }}</div>
                                </div>
                                <div class="flex flex-wrap gap-1.5 justify-end">
                                    <span class="inline-flex items-center text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $cr->callTypeBadgeClass }}">
                                        {{ strtoupper($cr->callTypeLabel) }}
                                    </span>
                                    <span class="inline-flex items-center text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $cr->statusBadgeClass }}">
                                        {{ strtoupper($cr->status) }}
                                    </span>
                                </div>
                            </div>
                            @if($cr->notes)
                                <div class="mb-2 p-2.5 bg-gray-50 rounded-lg text-xs text-gray-700 border border-gray-100">
                                    <div class="font-bold text-[10px] text-gray-500 uppercase tracking-wide mb-0.5">📝 Catatan HR:</div>
                                    {{ \Illuminate\Support\Str::limit($cr->notes, 140) }}
                                </div>
                            @endif
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $cr->created_at->format('d M Y H:i') }}
                                </span>
                                @if($cr->status === 'pending')
                                    <div class="flex gap-1.5">
                                        <button class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-red-50/60 text-blue-700 border border-blue-200 hover:bg-blue-100">Schedule</button>
                                        <button class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-green-50 text-green-700 border border-green-200 hover:bg-green-100">Done</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center border border-dashed border-gray-200 rounded-xl">
                        <div class="text-xs font-bold text-gray-500 mb-2">No Call Requests Yet</div>
                        <p class="text-[11px] text-gray-400">Permintaan Test/Interview dari HR Company akan muncul disini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-gradient-to-br from-gik-primary to-blue-900 rounded-2xl p-6 text-white shadow-lg">
            <h4 class="font-extrabold mb-1.5">Quick Matching Tips</h4>
            <ul class="space-y-1.5 text-xs text-white/80">
                <li class="flex items-start gap-1.5"><span>✓</span> Use skill tags for precise scoring.</li>
                <li class="flex items-start gap-1.5"><span>✓</span> Set Urgent tags to prioritize.</li>
                <li class="flex items-start gap-1.5"><span>✓</span> Re-run engine on CV updates.</li>
            </ul>
        </div>
    </div>
</div>

<!-- Add Talent Modal -->
<div id="addTalentModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4" onclick="if(event.target===this) this.classList.add('hidden'), this.classList.remove('flex')">
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <form method="POST" action="{{ route('gik-superadmin.talents.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <h3 class="font-extrabold text-gray-800 text-lg">+ Add Candidate • Ready to Hire</h3>
                <button type="button" onclick="document.getElementById('addTalentModal').classList.add('hidden'), document.getElementById('addTalentModal').classList.remove('flex')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">First Name <span class="text-red-600">*</span></label>
                        <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Last Name</label>
                        <input type="text" name="last_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Email</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Phone</label>
                        <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Current Job Title</label>
                        <input type="text" name="current_job_title" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Target Role</label>
                        <input type="text" name="target_role" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Years of Experience</label>
                        <input type="number" step="0.5" min="0" name="years_experience" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Expected Salary (IDR)</label>
                        <input type="number" name="expected_salary" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Skill Tags (comma separated)</label>
                    <input type="text" name="skill_tags" placeholder="React, TypeScript, NextJS" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Availability</label>
                        <select name="availability_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="available">Available now</option>
                            <option value="notice_period">Notice period</option>
                            <option value="employed">Currently Employed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">CV File</label>
                        <input type="file" name="cv" accept=".pdf,.doc,.docx" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gik-primary file:text-white">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 sticky bottom-0 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('addTalentModal').classList.add('hidden'), document.getElementById('addTalentModal').classList.remove('flex')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm">Add Candidate</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Client Need Modal -->
<div id="addNeedModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4" onclick="if(event.target===this) this.classList.add('hidden'), this.classList.remove('flex')">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
        <form method="POST" action="{{ route('gik-superadmin.client-needs.store') }}">
            @csrf
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-800 text-lg">+ New Client Hiring Need</h3>
                <button type="button" onclick="document.getElementById('addNeedModal').classList.add('hidden'), document.getElementById('addNeedModal').classList.remove('flex')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Client Company</label>
                    <select name="company_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                        @foreach(\App\Models\Client::all() as $c)
                            <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Job Title <span class="text-red-600">*</span></label>
                        <input type="text" name="job_title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Department</label>
                        <input type="text" name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Skill Tags (comma)</label>
                        <input type="text" name="skill_tags" placeholder="Figma, Design Systems" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Quota</label>
                        <input type="number" name="quota" value="1" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Urgency</label>
                        <select name="urgency" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="urgent">Urgent</option>
                            <option value="open" selected>Open</option>
                            <option value="low">Low Priority</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Deadline</label>
                        <input type="date" name="deadline" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('addNeedModal').classList.add('hidden'), document.getElementById('addNeedModal').classList.remove('flex')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm">Create Need</button>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('#addTalentModal, #addNeedModal').forEach(m => {
    const modalClasses = ['hidden','flex'];
    m.style.display = 'none';
    const observer = new MutationObserver(() => {
        if (m.classList.contains('hidden')) { m.style.display = 'none'; } else { m.style.display = 'flex'; }
    });
    observer.observe(m, { attributes: true, attributeFilter: ['class'] });
});
</script>
@endsection
