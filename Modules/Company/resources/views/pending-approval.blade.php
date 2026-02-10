@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh]">
        <div
            class="max-w-lg w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transform transition-all hover:scale-[1.01] duration-300">
            <div class="p-8 text-center">
                @php
                    $company = \Modules\Company\Models\Company::find(session('selected_company_id'));
                    $status = $company ? $company->status : 'unknown';
                @endphp

                @if($status === 'declined')
                    <div
                        class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce-slow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-red-600 dark:text-red-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Application Declined</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                        We're sorry, but your company application has been declined by the administrator.
                        Please contact support for more information or check your email for details.
                    </p>
                @else
                    <div
                        class="w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-10 h-10 text-yellow-600 dark:text-yellow-400 animate-pulse" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Application Pending Approval</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                        Your company application is currently under review by our administrators.
                        You will be notified once your application has been approved.
                    </p>
                    <div
                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 mb-6 border border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-left">
                                As a pending company, you cannot access dashboard features, manage teams, or perform procurement
                                activities until approved.
                            </p>
                        </div>
                    </div>
                @endif

                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-medium text-white transition-colors duration-200 bg-primary-600 rounded-xl hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-900 shadow-lg shadow-primary-600/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to My Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection