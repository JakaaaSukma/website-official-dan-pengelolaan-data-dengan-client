@extends(request()->routeIs('gik-superadmin.*') ? 'layouts.gik-superadmin' : 'layouts.gik-hrd')

@php
$isSA = request()->routeIs('gik-superadmin.*');
$rPrefix = $isSA ? 'gik-superadmin.' : 'gik-hrd.';
if ($isSA) {
    $newRequestLabel = 'Create Add Contract';
    $newRequestUrl = route('gik-superadmin.add-contract.create');
    $pageEyebrow = 'Super Admin • Cross-Tenant Operations';
    $pageTitle = 'Requests & Operations (All Companies)';
    $pageSubtitle = 'Monitoring & review semua request Add Contract / Extend / Off-boarding dari SEMUA tenant HR Company.';
} else {
    $newRequestLabel = 'New Request';
    $newRequestUrl = route('gik-hrd.request-hub');
    $pageEyebrow = 'HR Company • Operations';
    $pageTitle = 'Requests & Operations';
    $pageSubtitle = 'View and manage all operational requests (Add, Extend, Off-boarding).';
}
@endphp

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">{{ $pageEyebrow }}</div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">{{ $pageTitle }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $pageSubtitle }}</p>
    </div>
    <a href="{{ $newRequestUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-lg text-sm font-semibold hover:bg-gik-primary-dark transition shadow-sm shadow-red-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        {{ $newRequestLabel }}
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-wrap items-center gap-3 px-6 py-4 border-b border-gray-100">
        <div class="relative flex-1 min-w-[220px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Search by request number, name, or email..." oninput="window.location.href=this.value ? `${window.location.pathname}?q=${encodeURIComponent(this.value)}` : window.location.pathname" value="{{ request('q','') }}" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm">
        </div>
        <select onchange="window.location.href= this.value ? `${window.location.pathname}?${this.value}` : window.location.pathname" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">All Types</option>
            <option value="type=add_contract" {{ request('type')==='add_contract'?'selected':'' }}>Add Contract</option>
            <option value="type=extend_contract" {{ request('type')==='extend_contract'?'selected':'' }}>Extend Contract</option>
            <option value="type=off_boarding" {{ request('type')==='off_boarding'?'selected':'' }}>Off-boarding</option>
        </select>
        <select onchange="window.location.href= this.value ? `${window.location.pathname}?${this.value}` : window.location.pathname" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">All Status</option>
            <option value="status=draft" {{ request('status')==='draft'?'selected':'' }}>Draft</option>
            <option value="status=submitted" {{ request('status')==='submitted'?'selected':'' }}>Submitted</option>
            <option value="status=in_review" {{ request('status')==='in_review'?'selected':'' }}>In Review</option>
            <option value="status=approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
            <option value="status=rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
            <option value="status=completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
        </select>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Request</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Applicant</th>
                    <th class="px-6 py-3 text-left">Progress</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Updated</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-6 py-4 font-mono text-xs text-gray-700 font-semibold">{{ $req->request_number }}</td>
                        <td class="px-6 py-4">
                            @if($req->request_type === 'add_contract') <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">+ ADD</span>
                            @elseif($req->request_type === 'extend_contract') <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">↻ EXTEND</span>
                            @else <span class="text-xs font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">← OFF</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $req->full_name }}</div>
                            <div class="text-xs text-gray-500 truncate max-w-[220px]">{{ $req->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-40">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>Step {{ $req->current_step }}/{{ $req->total_steps }}</span>
                                    <span class="font-bold">{{ $req->overallCompletionPercentage() }}%</span>
                                </div>
                                <div class="gik-progress w-full"><div class="gik-progress-bar bg-gik-primary" style="width:{{ $req->overallCompletionPercentage() }}%"></div></div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $req->status_badge_class }}">{{ strtoupper(str_replace('_', ' ', $req->status)) }}</span></td>
                        <td class="px-6 py-4 text-xs text-gray-500">{{ $req->updated_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right space-x-1.5">
                            @if(($req->status === 'draft' || $req->status === 'rejected') && !$isSA)
                                <a href="{{ route($rPrefix . 'requests.edit', $req) }}" class="text-xs font-semibold text-gik-accent hover:underline inline-flex items-center gap-1 px-2 py-1">Edit</a>
                                <form method="POST" action="{{ route($rPrefix . 'requests.destroy', $req) }}" onsubmit="return confirm('Delete this request?')" class="inline">@csrf @method('DELETE')<button class="text-xs font-semibold text-red-600 hover:underline inline-flex items-center gap-1 px-2 py-1">Delete</button></form>
                            @else
                                <a href="{{ route($rPrefix . 'requests.edit', $req) }}" class="text-xs font-semibold text-gray-600 hover:text-gik-accent hover:underline inline-flex items-center gap-1 px-2 py-1">{{ $isSA ? 'Review' : 'View' }}</a>
                                @if($isSA && in_array($req->status, ['submitted', 'in_review']))
                                    <form method="POST" action="{{ route('gik-superadmin.requests.review-batch', $req) }}" class="inline">@csrf<button type="submit" class="text-xs font-bold text-green-700 hover:underline px-2 py-1 bg-green-50 rounded">✓ Quick Review</button></form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <h3 class="font-bold text-gray-700 mb-1">No requests yet</h3>
                        <p class="text-sm text-gray-500 mb-3">Create your first operational request via Pusat Request.</p>
                        <a href="{{ route('gik-hrd.request-hub') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-lg text-sm font-semibold hover:bg-gik-primary-dark transition">Go to Request Hub</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
