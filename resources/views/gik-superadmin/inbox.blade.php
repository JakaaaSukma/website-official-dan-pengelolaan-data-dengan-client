@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Communications</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Client Inbox</h1>
        <p class="text-gray-500 text-sm mt-1">Review, reply, and resolve requests from HRD Clients.</p>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-7 py-5 border-b border-gray-100 flex flex-wrap items-center gap-3">
        <div class="flex gap-1 p-1 bg-gray-100/80 rounded-xl">
            @php
                $curStatus = request('status', 'all');
                $tabs = [
                    ['all','All', $messagesTotal ?? 0],
                    ['unread','Unread', $unread ?? 0],
                    ['replied','Replied', $replied ?? 0],
                    ['resolved','Resolved', $resolved ?? 0],
                ];
            @endphp
            @foreach($tabs as $t)
                <a href="{{ route('gik-superadmin.inbox', ['status'=>$t[0]]) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition
                    {{ $curStatus === $t[0] ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">{{ $t[1] }}<span class="ml-1.5 text-[10px] text-gray-500 font-extrabold">{{ $t[2] ?? 0 }}</span></a>
            @endforeach
        </div>
        <select onchange="window.location.href= this.value ? `${window.location.pathname}?${this.value}` : window.location.pathname" class="ml-auto px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">All Types</option>
            <option value="type=general">General</option>
            <option value="type=quota_request">Quota Request</option>
            <option value="type=account_issue">Account Issue</option>
            <option value="type=performance_report">Performance Report</option>
            <option value="type=request_access">Access Request</option>
        </select>
    </div>
    <div class="divide-y divide-gray-100 max-h-[65vh] overflow-y-auto">
        @forelse($messages as $m)
            <div id="msg-{{ $m->id }}" class="px-7 py-5 hover:bg-red-50/60/30 transition {{ $m->status === 'unread' ? 'bg-red-50/60/40' : '' }}">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white font-extrabold shadow-sm flex-shrink-0">
                            {{ strtoupper(substr(optional($m->company)->company_name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-0.5">
                                <span class="font-bold text-gray-800 truncate">{{ optional($m->company)->company_name ?? 'System' }}</span>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    @if($m->message_type === 'quota_request') bg-purple-100 text-purple-700
                                    @elseif($m->message_type === 'account_issue') bg-orange-100 text-orange-700
                                    @elseif($m->message_type === 'performance_report') bg-green-100 text-green-700
                                    @elseif($m->message_type === 'request_access') bg-amber-100 text-amber-700
                                    @else bg-blue-100 text-blue-700 @endif
                                ">{{ str_replace('_', ' ', $m->message_type) }}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($m->priority === 'urgent') bg-red-100 text-red-700
                                    @elseif($m->priority === 'high') bg-orange-100 text-orange-700
                                    @elseif($m->priority === 'low') bg-gray-100 text-gray-600
                                    @else bg-blue-100 text-blue-700 @endif
                                ">{{ ucfirst($m->priority) }}</span>
                                @if($m->status === 'unread')<span class="text-[10px] font-extrabold text-white bg-blue-600 px-1.5 py-0.5 rounded">NEW</span>@endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span>From: {{ $m->from_user_id ? optional($m->fromUser)->name : 'Client HRD' }}</span>
                                <span>•</span>
                                <span>{{ $m->created_at->format('M d, Y H:i') }} ({{ $m->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold
                            @if($m->status === 'resolved') bg-green-100 text-green-700
                            @elseif($m->status === 'replied') bg-blue-100 text-blue-700
                            @elseif($m->status === 'read') bg-gray-100 text-gray-600
                            @else bg-amber-100 text-amber-700 @endif
                        ">{{ strtoupper(str_replace('_', ' ', $m->status)) }}</span>
                    </div>
                </div>
                <div class="pl-14">
                    <div class="text-sm font-semibold text-gray-800 mb-1">{{ $m->subject ?? '(No Subject)' }}</div>
                    <p class="text-sm text-gray-600 leading-relaxed mb-3">{{ $m->body }}</p>
                    @if($m->replies && $m->replies->count() > 0)
                        <div class="space-y-3 mb-4">
                            @foreach($m->replies as $reply)
                                @if($reply->user_id === auth()->id() || optional($reply->user)->role === 'admin')
                                    <div class="flex justify-end">
                                        <div class="bg-[#eef2ff] border border-blue-100 rounded-xl p-3 max-w-[85%]">
                                            <div class="flex items-center gap-2 mb-1.5 justify-end">
                                                <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                <span class="text-xs font-bold text-gik-primary">You</span>
                                            </div>
                                            <p class="text-sm text-gray-700 leading-relaxed text-right">{{ $reply->body }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex justify-start">
                                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 max-w-[85%]">
                                            <div class="flex items-center gap-2 mb-1.5 justify-start">
                                                <span class="text-xs font-bold text-gray-700">{{ optional($reply->user)->name ?? 'Client' }}</span>
                                                <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-700 leading-relaxed">{{ $reply->body }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div class="flex flex-wrap items-center gap-2">
                        @if($m->status !== 'resolved')
                            <button type="button" onclick="toggleReply({{ $m->id }})" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-xs font-bold transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Reply
                            </button>
                        @endif
                        @if($m->status !== 'resolved')
                            <form method="POST" action="{{ route('gik-superadmin.inbox.resolve', $m) }}">
                                @csrf
                                <button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 border-2 border-green-600 text-green-700 hover:bg-green-50 rounded-lg text-xs font-bold transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Mark Resolved
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('gik-superadmin.inbox.assign', $m) }}" class="flex items-center gap-1.5">
                            @csrf
                            <select name="assigned_to" onchange="if(this.value) this.form.submit()" class="px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <option value="">Assign to...</option>
                                <option value="recruitment1@gyokai.com">Recruitment Officer 1</option>
                                <option value="recruitment2@gyokai.com">Recruitment Officer 2</option>
                            </select>
                        </form>
                    </div>

                    <div id="reply-box-{{ $m->id }}" class="hidden mt-4 pl-14">
                        <form method="POST" action="{{ route('gik-superadmin.inbox.reply', $m) }}" class="bg-white rounded-xl border-2 border-gik-accent/30 p-4">
                            @csrf
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Your Reply</label>
                            <textarea name="reply" rows="3" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 mb-2"></textarea>
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="toggleReply({{ $m->id }})" class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                                <button type="submit" class="px-4 py-1.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-xs font-bold transition">Send Reply</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                <h3 class="font-bold text-gray-700 mb-1">No messages found.</h3>
                <p class="text-sm text-gray-500">Try different filters or come back later.</p>
            </div>
        @endforelse
    </div>
    @if($messages->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-3xl">{{ $messages->links() }}</div>
    @endif
</div>
<script>
function toggleReply(id){
    const el = document.getElementById('reply-box-' + id);
    if(el.classList.contains('hidden')) { el.classList.remove('hidden'); el.scrollIntoView({behavior:'smooth', block:'center'}); }
    else el.classList.add('hidden');
}
</script>
@endsection
