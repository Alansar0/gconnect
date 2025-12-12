

<x-layouts.app>
    <div class="min-h-screen bg-bg1 text-t1 font-['Roboto'] p-4">

        <!-- Back Button -->
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}"
               class="text-accent hover:underline flex items-center transition-all duration-300 hover:opacity-80">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Notification Card -->
        <div class="bg-bg2 rounded-2xl border border-accent p-6 
                    shadow-accent transition-all duration-300 
                    hover:shadow-[0_0_25px_var(--shadow-accent)]">

            <!-- Header -->
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ Vite::asset('resources/images/logo.png') }}"
                     class="w-14 h-14 rounded-full border border-accent shadow-accent" />

                <div>
                    <p class="text-lg font-semibold text-t1">
                        {{ $notification->data['title'] ?? 'Notification' }}
                    </p>
                    <p class="text-xs text-accent tracking-wide">
                        {{ $notification->data['subtitle'] ?? 'Gconnect Notification' }}
                    </p>
                </div>
            </div>

            <!-- Message Body -->
            <div class="text-sm leading-relaxed text-t2 mb-6 border-t border-accent pt-4">
                {{ $notification->data['message'] ?? 'No additional message provided.' }}
            </div>

            <!-- Footer Actions -->
            <div class="flex justify-between items-center">

                <!-- Timestamp -->
                <span class="text-xs text-accent">
                    {{ $notification->created_at->diffForHumans() }} •
                    {{ $notification->created_at->format('M d, Y h:i A') }}
                </span>

                <!-- Read Button or Status -->
                @if(!$notification->read_at)
                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="bg-bg3 hover:bg-bg2 text-t1 px-4 py-2 rounded-md 
                                       text-sm border border-accent transition-all duration-300 
                                       hover:shadow-accent">
                            Mark as Read
                        </button>
                    </form>
                @else
                    <span class="text-green-400 text-sm flex items-center gap-1">
                        ✅ Read
                    </span>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>

