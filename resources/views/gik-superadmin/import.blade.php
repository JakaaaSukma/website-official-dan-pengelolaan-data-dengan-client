@extends('layouts.gik-superadmin')

@section('content')
@php
    $modeMap = [
        'talents'    => ['title' => 'Import Talent Pool (Global)',        'back' => route('gik-superadmin.talents'),           'upload' => route('gik-superadmin.talents.import.process'),          'tpl' => route('gik-superadmin.talents.template')],
        'employees'  => ['title' => 'Import Employees / Peserta',        'back' => route('gik-superadmin.employees'),         'upload' => route('gik-superadmin.employees.import.process'),        'tpl' => route('gik-superadmin.employees.template')],
        'candidates' => ['title' => 'Import Company Candidates',         'back' => route('gik-superadmin.candidates'),        'upload' => route('gik-superadmin.candidates.import.process'),       'tpl' => route('gik-superadmin.candidates.template')],
    ];
    $m = $modeMap[$mode] ?? $modeMap['talents'];
@endphp

<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-[28px] font-extrabold text-gik-primary-dark leading-tight">{{ $m['title'] }}</h1>
        <p class="text-gray-500 text-sm mt-1">Upload Excel .xlsx. Validate & preview before commit to DB (full rollback on failure).</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ $m['tpl'] }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-green-600 text-green-700 bg-green-50 hover:bg-green-100 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download Template
        </a>
        <a href="{{ $m['back'] }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
            Back
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            @if(!session('preview_valid_rows') && !session('import_errors'))
                <h3 class="font-bold text-gray-800 mb-4">Upload Excel File</h3>
                <form method="POST" action="{{ $m['upload'] }}" enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-10 text-center bg-gray-50 hover:border-gik-accent hover:bg-red-50/60 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <h4 class="font-bold text-gray-800 mb-1">Click to select or drag and drop file</h4>
                        <p class="text-xs text-gray-500 mb-4">Format .xlsx • Max 10 MB • Maks 500 baris per import</p>
                        <input type="file" name="excel_file" accept=".xlsx" required class="block mx-auto text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gik-primary file:text-white hover:file:bg-blue-900 cursor-pointer">
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition inline-flex items-center gap-2 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Validate & Preview
                        </button>
                    </div>
                </form>
            @else
                <h3 class="font-bold text-gray-800 mb-4">Validation Preview</h3>
                @if($errors_list = session('import_errors'))
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-sm">
                    <div class="flex items-center gap-2 mb-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-bold text-red-700">{{ count($errors_list) }} records gagal validasi</span></div>
                    <div class="max-h-40 overflow-y-auto space-y-1">
                        @foreach(array_slice($errors_list, 0, 10) as $e)
                            <div class="text-xs text-red-700">Row {{ $e['row'] ?? '?' }}: {{ $e['error'] ?? '' }}</div>
                        @endforeach
                        @if(count($errors_list) > 10)
                            <div class="text-xs text-red-600 italic">... {{ count($errors_list) - 10 }} error lainnya tersembunyi</div>
                        @endif
                    </div>
                </div>
                @endif
                @if($valid_rows = session('preview_valid_rows'))
                @php 
                    $previewData = session('employee_preview') ?? session('talent_preview') ?? session('preview_data');
                    $validCount = $previewData['valid_count'] ?? count($valid_rows);
                    $storedPath = $previewData['stored_path'] ?? '';
                @endphp
                <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl text-sm">
                    <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-bold text-green-700">{{ $validCount }} records siap diimport • transaksi DB dijamin atomic</span></div>
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-xl max-h-[380px] overflow-y-auto">
                    <table class="w-full text-xs min-w-[600px]">
                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider sticky top-0 z-10">
                            <tr>
                                @foreach(array_keys((array) $valid_rows[0]) as $col)
                                    <th class="px-3 py-2 text-left whitespace-nowrap">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach(array_slice($valid_rows, 0, 10) as $row)
                                <tr class="hover:bg-gray-50">
                                    @foreach($row as $cell)
                                        <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ \Illuminate\Support\Str::limit($cell, 40) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($validCount > 10)
                    <div class="text-xs text-center text-gray-500 mt-3">Preview 10 / {{ $validCount }} records.</div>
                @endif
                <form method="POST" action="{{ $m['upload'] }}" class="mt-6 flex justify-end gap-3">
                    @csrf
                    <input type="hidden" name="stored_path" value="{{ $storedPath }}">
                    <button type="submit" name="cancel" value="1" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" name="confirm_import" value="1" class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-bold transition inline-flex items-center gap-2 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Confirm Import ({{ $validCount }} records)
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Security Checks</div>
            <ul class="space-y-2.5 text-xs text-gray-600">
                <li class="flex items-center gap-2"><span class="text-green-600 font-extrabold">✓</span> MIME type validation (.xlsx only)</li>
                <li class="flex items-center gap-2"><span class="text-green-600 font-extrabold">✓</span> Max file size: 10 MB</li>
                <li class="flex items-center gap-2"><span class="text-green-600 font-extrabold">✓</span> Formula Injection Sanitization (= + - @ prefix)</li>
                <li class="flex items-center gap-2"><span class="text-green-600 font-extrabold">✓</span> DB Transaction (full rollback on fail)</li>
                <li class="flex items-center gap-2"><span class="text-green-600 font-extrabold">✓</span> Audit log per-activity</li>
            </ul>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Tips Penggunaan</div>
            <ul class="space-y-2 text-xs text-gray-600">
                <li>→ Gunakan template official untuk menghindari kolom tidak terbaca.</li>
                <li>→ Hanya sheet pertama yang diproses. Sheet lain diabaikan.</li>
                <li>→ Baris pertama adalah nama kolom HEADER (tidak di-insert).</li>
                <li>→ Format tanggal: YYYY-MM-DD atau format Excel standar.</li>
                <li>→ Hasil import dicatat di table hr_import_logs (append-only).</li>
            </ul>
        </div>
    </div>
</div>
@endsection
