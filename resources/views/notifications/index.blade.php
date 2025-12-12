
<x-layouts.app>
    <div class="min-h-screen bg-bg1 text-t1 font-['Roboto'] flex flex-col">

        <!-- Back Button + Mark All as Read -->
        <div class="w-full flex justify-between items-center mt-6 mb-4 px-4">
            
            <!-- Back -->
            <a href="{{ url()->previous() }}"
               class="text-accent hover:underline flex items-center transition-all duration-300 hover:opacity-80">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>

            <!-- Mark All as Read -->
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit"
                        class="bg-bg2 hover:bg-bg3 text-t1 px-3 py-1.5 rounded-md text-sm transition-all duration-300 border border-accent hover:shadow-accent">
                    Mark All as Read
                </button>
            </form>
        </div>


        <!-- Header -->
        <div class="w-full text-center mt-2 px-6">
            <h1 class="text-2xl font-bold text-accent mb-6 tracking-wide">
                Notifications
            </h1>
        </div>

        <!-- Notification List -->
        <div id="notification-list" class="flex flex-col px-5 space-y-3">
            @forelse ($notifications as $notification)
                <a href="{{ route('notifications.show', $notification->id) }}"
                   class="flex items-center gap-3 p-4 border border-accent rounded-xl 
                          bg-bg2 shadow-accent hover:shadow-[0_0_20px_var(--shadow-accent)] 
                          transition-all duration-300">

                    <!-- App Icon -->
                    <img src="{{ Vite::asset('resources/images/logo.png') }}"
                         class="w-10 h-10 rounded-full border border-accent shadow-accent" />

                    <div class="flex flex-col">
                        <!-- Title -->
                        <p class="text-sm font-semibold text-t1">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </p>

                        <!-- Message -->
                        <p class="text-xs text-t2 leading-snug">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        <!-- Time -->
                        <small class="text-accent text-[11px] mt-1">
                            {{ $notification->created_at->diffForHumans() }} •
                            {{ $notification->created_at->format('M d, Y h:i A') }}
                        </small>
                    </div>
                </a>
            @empty
                <p class="text-center text-t3 mt-10 text-sm">
                    You have no notifications at the moment.
                </p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-6 text-center text-t1">
            {{ $notifications->links() }}
        </div>
    </div>
</x-layouts.app>
