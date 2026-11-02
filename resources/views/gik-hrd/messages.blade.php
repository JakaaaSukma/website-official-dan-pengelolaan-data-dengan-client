@extends('layouts.gik-hrd')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Messages</h1>
        <p class="text-gray-500 text-sm mt-1">Communicate directly with the Recruitment Team.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Message Inbox</h3>
        </div>
        <div class="divide-y divide-gray-100 max-h-[550px] overflow-y-auto">
            @forelse($messages as $m)
                <div class="px-6 py-4 hover:bg-gray-50/60 transition">
                    <div class="flex items-start justify-between gap-4 mb-2">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                                    @if($m->message_type === 'quota_request') bg-purple-100 text-purple-700
                                    @elseif($m->message_type === 'account_issue') bg-orange-100 text-orange-700
                                    @elseif($m->message_type === 'performance_report') bg-green-100 text-green-700
                                    @elseif($m->message_type === 'request_access') bg-amber-100 text-amber-700
                                    @else bg-blue-100 text-blue-700 @endif
                                ">{{ str_replace('_', ' ', $m->message_type) }}</span>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full
                                    @if($m->priority === 'urgent') bg-red-100 text-red-700
                                    @elseif($m->priority === 'high') bg-orange-100 text-orange-700
                                    @elseif($m->priority === 'low') bg-gray-100 text-gray-600
                                    @else bg-blue-100 text-blue-700 @endif">{{ ucfirst($m->priority) }}</span>
                                @if($m->status === 'unread')<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-50/60 text-blue-700">● NEW</span>@endif
                            </div>
                            <div class="text-xs text-gray-500">{{ $m->created_at->diffForHumans() }} • From: {{ $m->from_user_id ? 'You' : 'System' }} • To: {{ $m->to_user_id ? 'Recruitment Team' : 'HRD' }}</div>
                        </div>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $m->status === 'resolved' ? 'bg-green-100 text-green-700' : ($m->status === 'replied' ? 'bg-blue-100 text-blue-700' : ($m->status === 'read' ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-700')) }}">{{ strtoupper(str_replace('_', ' ', $m->status)) }}</span>
                    </div>
                    <div class="text-sm text-gray-700 leading-relaxed mb-3 truncate max-w-2xl"><span class="font-semibold text-gray-800">{{ $m->subject ?? 'No Subject' }}:</span> {{ $m->body }}</div>
                    <div class="flex items-center gap-2 mt-3">
                        <a href="{{ route('gik-hrd.messages.show', $m) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            Lihat Percakapan
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                    <h3 class="font-bold text-gray-700 mb-1">No messages yet</h3>
                    <p class="text-sm text-gray-500">Your conversation history will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
        <h3 class="font-bold text-gray-800 mb-4">Send New Message</h3>
        <form method="POST" action="{{ route('gik-hrd.messages.send') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Message Type</label>
                <select name="message_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="general">General Inquiry</option>
                    <option value="quota_request">Quota Request</option>
                    <option value="account_issue">Account Issue</option>
                    <option value="performance_report">Performance Report</option>
                    <option value="request_access">Access Request</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Priority</label>
                <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="normal">Normal</option>
                    <option value="low">Low</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subject</label>
                <input type="text" name="subject" required maxlength="150" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Message Body</label>
                <textarea name="body" rows="5" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition inline-flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send Message
            </button>
        </form>
    </div>
</div>
@endsection
