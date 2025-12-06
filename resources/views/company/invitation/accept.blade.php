@extends('layouts.guest')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gray-50 dark:bg-gray-900">
        <div
            class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">

            {{-- Header --}}
            <div class="bg-primary-600 px-8 py-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-white/10 opacity-30 transform -skew-y-6 origin-top-left">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i data-feather="mail" class="w-8 h-8 text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">You're Invited!</h2>
                    <p class="text-primary-100 text-sm">Join <strong>{{ $invitation->company->name }}</strong> on NeoUX</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="p-8">
                <form action="{{ route('team.process-acceptance') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $invitation->token }}">

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email
                                Address</label>
                            <input type="email" value="{{ $invitation->email }}" disabled
                                class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full
                                Name</label>
                            <input type="text" name="name" required placeholder="Enter your full name" autofocus
                                class="block w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Create
                                Password</label>
                            <input type="password" name="password" required placeholder="Min. 8 characters"
                                class="block w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm
                                Password</label>
                            <input type="password" name="password_confirmation" required placeholder="Re-enter password"
                                class="block w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all sm:text-sm">
                        </div>

                        <button type="submit"
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all transform hover:-translate-y-0.5">
                            Create Account & Join Team
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-4">
                            By joining, you agree to our Terms of Service and Privacy Policy.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection