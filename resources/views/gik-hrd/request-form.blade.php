@extends('layouts.gik-hrd')

@section('content')
@php
    $typeLabel = $request->request_type_label;
    $steps = $request->steps_config;
    $completion = $request->overallCompletionPercentage();
    $isOffboarding = $request->request_type === 'off_boarding';
    $canGoNext = true;
@endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">
            @if($request->request_type === 'add_contract') New Contract Request
            @elseif($request->request_type === 'extend_contract') Extend Contract Request
            @else Off-boarding Request
            @endif
        </h1>
        <p class="text-gray-500 text-sm mt-1">Complete the operational form to initialize @if($isOffboarding) an employee departure process. @else a new employee contract. @endif</p>
        <div class="text-xs text-gray-400 mt-2">Request Number: <span class="font-mono font-semibold text-gray-600">{{ $request->request_number }}</span> &nbsp;•&nbsp; Status: <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $request->status_badge_class }}">{{ strtoupper(str_replace('_', ' ', $request->status)) }}</span></div>
    </div>
    <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('gik-hrd.requests.save-draft', $request) }}" id="saveDraftForm">
            @csrf
            <button type="submit" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Save Draft
            </button>
        </form>
        @if($request->status === 'draft' && $currentStep >= $request->total_steps)
            <form method="POST" action="{{ route('gik-hrd.requests.submit', $request) }}" onsubmit="return confirm('Submit request ini untuk direview Tim Rekrutmen?')">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold inline-flex items-center gap-2 transition shadow-sm">
                    Submit Request
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- LEFT: Steps panel -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Request Status Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Request Status</h2>
            <div class="space-y-0 mb-6">
                @foreach($steps as $n => $step)
                    @php
                        $isCurrent = $n === $currentStep;
                        $isPast = $n < $currentStep;
                        $isLocked = $n > $currentStep;
                    @endphp
                    <div class="relative flex items-start gap-3 py-2.5 {{ $loop->last ? '' : '' }}">
                        @if(!$loop->last)
                            <div class="absolute left-5 top-[38px] w-0.5 h-7 bg-gray-200"></div>
                        @endif
                        <div class="relative z-10 w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-xs mt-0.5
                            {{ $isPast ? 'bg-gik-primary text-white' : ($isCurrent ? 'bg-gik-primary ring-4 ring-blue-100 text-white' : 'bg-gray-200 text-gray-400') }}">
                            @if($isPast)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $n }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-gray-400">Step {{ $n }}</div>
                            <div class="text-sm font-bold {{ $isCurrent ? 'text-gray-900' : ($isPast ? 'text-gray-700' : 'text-gray-400') }}">
                                {{ $step['name'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Overall Completion -->
            <div class="pt-5 border-t border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-600">Overall Completion</span>
                    <span class="text-sm font-extrabold text-gray-700">{{ $completion }}%</span>
                </div>
                <div class="gik-progress w-full">
                    <div class="gik-progress-bar bg-gik-primary transition-all" style="width: {{ $completion }}%"></div>
                </div>
            </div>
        </div>

        <!-- Need Assistance -->
        <div class="bg-[#f5f7ff] rounded-2xl border border-blue-100 p-5">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-blue-200/60 text-gik-primary flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-gik-primary-dark mb-1.5">Need Assistance?</div>
                    <div class="text-xs text-gray-600 leading-relaxed">
                        Ensure all mandatory fields marked with an asterisk (*) are filled before proceeding to the final submission.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Form -->
    <div class="lg:col-span-8 space-y-6">
        <form method="POST" action="{{ route('gik-hrd.requests.save-step', $request) }}" enctype="multipart/form-data" id="stepForm">
            @csrf
            <input type="hidden" name="step" value="{{ $currentStep }}">
            <input type="hidden" name="next_step" value="{{ $currentStep }}" id="nextStepInput">

            @php
                $stepBg = 'bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden';
                $stepTitleIcon = match($currentStep) {
                    1 => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    2 => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    default => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                };
            @endphp

            <!-- STEP 1: Personal Data -->
            @if($currentStep >= 1)
                <div class="{{ $stepBg }} {{ $currentStep === 1 ? 'ring-2 ring-gik-primary' : '' }}">
                    <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100 bg-[#f6f7fc]">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg text-gik-primary flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Personal Data</h3>
                        </div>
                        @if($currentStep > 1)
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2.5 py-1 rounded-full">COMPLETED</span>
                        @endif
                    </div>
                    <div class="p-7 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">First Name <span class="text-red-600">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $request->first_name) }}" {{ $currentStep !== 1 ? 'disabled' : '' }} required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last Name <span class="text-red-600">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $request->last_name) }}" {{ $currentStep !== 1 ? 'disabled' : '' }} required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-600">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $request->email) }}" {{ $currentStep !== 1 ? 'disabled' : '' }} required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $request->phone) }}" {{ $currentStep !== 1 ? 'disabled' : '' }} placeholder="+1 (555) 000-0000" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">National ID / SSN</label>
                            <input type="text" name="national_id" value="{{ old('national_id', $request->national_id ? \App\Helpers\HrAuditHelper::maskNationalId($request->national_id) : '') }}" {{ $currentStep !== 1 ? 'disabled' : '' }} placeholder="XXX-XX-XXXX" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                        </div>

                        @if($isOffboarding && $currentStep === 1)
                            <div class="md:col-span-2 pt-4 border-t border-dashed border-gray-200">
                                <h4 class="font-bold text-gray-800 mb-3 text-sm">Off-boarding Details (Step 1 for this type)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Effective End Date <span class="text-red-600">*</span></label>
                                        <input type="date" name="off_boarding_date" value="{{ old('off_boarding_date', optional($request->off_boarding_date)->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Reason for Leaving <span class="text-red-600">*</span></label>
                                        <textarea name="off_boarding_reason" rows="3" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">{{ old('off_boarding_reason', $request->off_boarding_reason) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($currentStep === 1)
                        <div class="px-7 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                            <button type="button" onclick="submitNextStep(2)" class="px-6 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold inline-flex items-center gap-2 transition">
                                Continue to {{ $steps[2]['name'] }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- STEP 2: Contract / Off-boarding Details -->
            @if($currentStep >= 2)
                <div class="{{ $stepBg }} {{ $currentStep === 2 ? 'ring-2 ring-gik-primary' : 'opacity-90' }}">
                    @if($currentStep === 2 && !$isOffboarding)
                        <div class="flex items-center justify-center px-7 py-1 border-b border-gray-100">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 py-1 border-t-2 border-gik-primary px-4">STEP 2 • NOW EDITING</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100 bg-[#f6f7fc]">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg text-gik-primary flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">
                                @if($isOffboarding) Off-boarding Details @else Contract Details @endif
                            </h3>
                        </div>
                        @if($currentStep > 2)
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2.5 py-1 rounded-full">COMPLETED</span>
                        @endif
                    </div>
                    <div class="p-7 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        @if($isOffboarding)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Effective End Date <span class="text-red-600">*</span></label>
                                <input type="date" name="off_boarding_date" value="{{ old('off_boarding_date', optional($request->off_boarding_date)->format('Y-m-d')) }}" {{ $currentStep !== 2 ? 'disabled' : '' }} required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Reason for Leaving <span class="text-red-600">*</span></label>
                                <textarea name="off_boarding_reason" rows="3" {{ $currentStep !== 2 ? 'disabled' : '' }} required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">{{ old('off_boarding_reason', $request->off_boarding_reason) }}</textarea>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contract Type <span class="text-red-600">*</span></label>
                                <select name="contract_type" {{ $currentStep !== 2 ? 'disabled' : '' }} required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600 bg-white">
                                    <option value="">Select contract type</option>
                                    <option value="internship" {{ old('contract_type', $request->contract_type) === 'internship' ? 'selected' : '' }}>Internship</option>
                                    <option value="full_time" {{ old('contract_type', $request->contract_type) === 'full_time' ? 'selected' : '' }}>Full-Time Permanent</option>
                                    <option value="part_time" {{ old('contract_type', $request->contract_type) === 'part_time' ? 'selected' : '' }}>Part-Time</option>
                                    <option value="contract" {{ old('contract_type', $request->contract_type) === 'contract' ? 'selected' : '' }}>Contract (Fixed Term)</option>
                                    <option value="probation" {{ old('contract_type', $request->contract_type) === 'probation' ? 'selected' : '' }}>Probation</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Department <span class="text-red-600">*</span></label>
                                <select name="department" {{ $currentStep !== 2 ? 'disabled' : '' }} required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600 bg-white">
                                    <option value="">Select department</option>
                                    <option value="Engineering" {{ old('department', $request->department) === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                    <option value="Software Engineering" {{ old('department', $request->department) === 'Software Engineering' ? 'selected' : '' }}>Software Engineering</option>
                                    <option value="UI/UX Design" {{ old('department', $request->department) === 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                    <option value="Product" {{ old('department', $request->department) === 'Product' ? 'selected' : '' }}>Product</option>
                                    <option value="Marketing" {{ old('department', $request->department) === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="HR" {{ old('department', $request->department) === 'HR' ? 'selected' : '' }}>HR</option>
                                    <option value="Finance" {{ old('department', $request->department) === 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Operations" {{ old('department', $request->department) === 'Operations' ? 'selected' : '' }}>Operations</option>
                                    <option value="Sales" {{ old('department', $request->department) === 'Sales' ? 'selected' : '' }}>Sales</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Job Title <span class="text-red-600">*</span></label>
                                <input type="text" name="job_title" value="{{ old('job_title', $request->job_title) }}" {{ $currentStep !== 2 ? 'disabled' : '' }} placeholder="e.g. Senior Software Engineer" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                            </div>
                            <div class="md:col-span-2">
                                <div class="bg-[#f6f7fc] rounded-xl p-5 border border-gray-100">
                                    <div class="text-sm font-bold text-gray-700 mb-3">Contract Duration</div>
                                    <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4 items-end">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Start Date</label>
                                            <div class="relative">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <input type="date" name="contract_start" value="{{ old('contract_start', optional($request->contract_start)->format('Y-m-d')) }}" {{ $currentStep !== 2 ? 'disabled' : '' }} required class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                                            </div>
                                        </div>
                                        <div class="text-sm font-bold text-gray-500 pb-2 text-center">to</div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">End Date</label>
                                            <div class="relative">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <input type="date" name="contract_end" value="{{ old('contract_end', optional($request->contract_end)->format('Y-m-d')) }}" {{ $currentStep !== 2 ? 'disabled' : '' }} required class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 disabled:bg-gray-50 disabled:text-gray-600">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($currentStep === 2)
                        <div class="px-7 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                            <button type="button" onclick="document.getElementById('nextStepInput').value=1; document.getElementById('stepForm').submit();" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-100 transition inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                                Back to {{ $steps[1]['name'] }}
                            </button>
                            <button type="button" onclick="submitNextStep(3)" class="px-6 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold inline-flex items-center gap-2 transition">
                                Continue to Documents
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- STEP 3: Documents -->
            @if($currentStep >= 3)
                @php $isLocked = $currentStep < 3; @endphp
                <div class="{{ $stepBg }} relative">
                    @if($isLocked) <div class="absolute inset-0 bg-gray-50/60 z-10 flex items-center justify-center rounded-2xl"><div class="bg-white shadow-lg rounded-xl px-5 py-3 text-sm font-semibold text-gray-500 inline-flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>Complete previous steps first</div></div> @endif
                    <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100 bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg text-gray-500 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-500">Documents</h3>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div class="p-7">
                        @if($request->documents->isNotEmpty())
                            <div class="mb-4 space-y-2">
                                @foreach($request->documents as $doc)
                                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg bg-gray-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800">{{ $doc->document_name }}</div>
                                                <div class="text-xs text-gray-500">{{ strtoupper($doc->document_type) }}</div>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-xs font-semibold text-gik-accent hover:underline px-2 py-1">Download</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if(!$isLocked)
                            <div id="documentFields" class="space-y-3 mb-4">
                                <div class="document-entry grid grid-cols-1 md:grid-cols-[1fr_1fr_2fr] gap-3 items-end p-4 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Document Type</label>
                                        <select name="document_types[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                                            <option value="ktp">KTP / National ID</option>
                                            <option value="kk">Family Card</option>
                                            <option value="ijazah">Diploma / Certificate</option>
                                            <option value="cv">CV / Resume</option>
                                            <option value="contract">Contract Document</option>
                                            <option value="medical">Medical Check-up</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Document Name</label>
                                        <input type="text" name="document_names[]" placeholder="e.g. KTP - Jane Doe" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    </div>
                                    <div>
                                        <input type="file" name="documents[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gik-primary file:text-white hover:file:bg-blue-900 cursor-pointer">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <button type="button" onclick="addDocumentField()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gik-accent hover:text-gik-primary hover:bg-red-50 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Add Another Document
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($currentStep === 3)
                        <div class="px-7 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                            <button type="button" onclick="document.getElementById('nextStepInput').value=2; document.getElementById('stepForm').submit();" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-100 transition inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                                Back
                            </button>
                            <button type="button" onclick="submitNextStep(3)" class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm font-bold inline-flex items-center gap-2 transition">
                                Save Documents
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            @if($currentStep >= $request->total_steps && $request->status === 'draft')
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="if(confirm('Submit request ini?')) { document.getElementById('nextStepInput').value={{ $request->total_steps }}; document.getElementById('stepForm').submit(); setTimeout(function(){ document.querySelector('form[action$=\"submit\"]').submit(); }, 800); }" class="px-7 py-3 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg font-bold inline-flex items-center gap-2 shadow-md">
                        Submit Request
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function submitNextStep(step) {
    document.getElementById('nextStepInput').value = step;
    document.getElementById('stepForm').submit();
}
function addDocumentField() {
    const tpl = document.querySelector('.document-entry').cloneNode(true);
    tpl.querySelectorAll('input, select').forEach(el => el.value = '');
    document.getElementById('documentFields').appendChild(tpl);
}
</script>
@endpush
