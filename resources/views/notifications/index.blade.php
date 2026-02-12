@extends('layouts.app', ['title' => 'Notifications'])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Notification History</h2>
            @if($notifications->count() > 0)
                <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            @forelse($notifications as $notification)
                <div onclick="markAsReadLocal('{{ $notification->id }}', '{{ $notification->data['url'] ?? '' }}')"
                    class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-start gap-4 cursor-pointer {{ $notification->read_at ? 'opacity-75' : 'bg-primary-50/10 dark:bg-primary-900/5' }}">
                    <div class="p-2.5 rounded-xl flex-shrink-0
                                            @if($notification->data['type'] == 'purchase_order') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($notification->data['type'] == 'goods_receipt') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($notification->data['type'] == 'new_offer') bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400
                                            @elseif($notification->data['type'] == 'offer_accepted') bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400
                                            @elseif($notification->data['type'] == 'new_comment') bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-400
                                            @else bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-400 @endif">

                        @if($notification->data['type'] == 'purchase_order') <i data-feather="file-text" class="w-5 h-5"></i>
                        @elseif($notification->data['type'] == 'goods_receipt') <i data-feather="truck" class="w-5 h-5"></i>
                        @elseif($notification->data['type'] == 'new_offer') <i data-feather="tag" class="w-5 h-5"></i>
                        @elseif($notification->data['type'] == 'offer_accepted') <i data-feather="award" class="w-5 h-5"></i>
                        @elseif($notification->data['type'] == 'new_comment') <i data-feather="message-circle"
                            class="w-5 h-5"></i>
                        @else <i data-feather="bell" class="w-5 h-5"></i> @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        <div class="flex items-center gap-3">
                            <span
                                class="text-xs font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition">
                                {{ $notification->data['action_text'] ?? 'View Details' }}
                            </span>

                            @if(!$notification->read_at)
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-600"></span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div
                        class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="bell" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-gray-900 dark:text-white font-bold mb-1">All caught up!</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">You don't have any new notifications.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection