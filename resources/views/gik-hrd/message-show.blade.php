@extends('layouts.gik-hrd')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('gik-hrd.messages') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-800 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Inbox
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[75vh]">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
        <div>
            <h2 class="font-extrabold text-gray-800 text-lg leading-tight">{{ $message->subject }}</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                    @if($message->message_type === 'quota_request') bg-purple-100 text-purple-700
                    @elseif($message->message_type === 'account_issue') bg-orange-100 text-orange-700
                    @elseif($message->message_type === 'performance_report') bg-green-100 text-green-700
                    @elseif($message->message_type === 'request_access') bg-amber-100 text-amber-700
                    @else bg-blue-100 text-blue-700 @endif
                ">{{ str_replace('_', ' ', $message->message_type) }}</span>
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full
                    @if($message->priority === 'urgent') bg-red-100 text-red-700
                    @elseif($message->priority === 'high') bg-orange-100 text-orange-700
                    @elseif($message->priority === 'low') bg-gray-100 text-gray-600
                    @else bg-blue-100 text-blue-700 @endif">{{ ucfirst($message->priority) }} Priority</span>
                <span class="text-xs text-gray-400 font-semibold">&bull; {{ $message->created_at->format('d M Y H:i') }}</span>
            </div>
        </div>
        <div class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $message->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-blue-50 text-blue-700' }}">
            {{ strtoupper(str_replace('_', ' ', $message->status)) }}
        </div>
    </div>

    <!-- Chat Body -->
    <div class="flex-1 overflow-y-auto p-6 bg-gray-50/30 space-y-4">
        <!-- Original Message -->
        <div class="flex justify-start">
            <div class="bg-white border border-gray-200 rounded-xl rounded-tl-sm p-4 max-w-[85%] shadow-sm">
                <div class="flex items-center gap-2 mb-2 justify-start">
                    <span class="text-xs font-bold text-gray-800">You ({{ optional($message->fromUser)->name ?? 'HR Company' }})</span>
                    <span class="text-[10px] text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $message->body }}</p>
            </div>
        </div>

        <!-- Replies -->
        @if($message->replies)
            @foreach($message->replies as $reply)
                @if($reply->user_id === auth()->id() || optional($reply->user)->role === 'company')
                    <div class="flex justify-start">
                        <div class="bg-white border border-gray-200 rounded-xl rounded-tl-sm p-4 max-w-[85%] shadow-sm">
                            <div class="flex items-center gap-2 mb-2 justify-start">
                                <span class="text-xs font-bold text-gray-800">You</span>
                                <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $reply->body }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex justify-end">
                        <div class="bg-[#eef2ff] border border-blue-100 rounded-xl rounded-tr-sm p-4 max-w-[85%] shadow-sm">
                            <div class="flex items-center gap-2 mb-2 justify-end">
                                <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                <span class="text-xs font-bold text-gik-primary">Recruitment Team</span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed text-right whitespace-pre-wrap">{{ $reply->body }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    <!-- Reply Form -->
    <div class="p-4 bg-white border-t border-gray-100 flex-shrink-0">
        @if($message->status !== 'resolved')
            <form method="POST" action="{{ route('gik-hrd.messages.reply', $message) }}" class="flex gap-3">
                @csrf
                <textarea name="reply" rows="2" placeholder="Tulis balasan pesan..." required class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:bg-white transition resize-none"></textarea>
                <button type="submit" class="px-6 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl font-bold transition shadow-sm hover:shadow-md flex items-center gap-2">
                    Kirim
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        @else
            <div class="text-center py-2 text-sm text-gray-500 font-semibold bg-gray-50 rounded-xl border border-gray-100">
                Pesan ini telah ditandai sebagai Selesai (Resolved).
            </div>
        @endif
    </div>
</div>
@endsection
