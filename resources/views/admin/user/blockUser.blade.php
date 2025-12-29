<x-layouts.admin>
    <div class="min-h-screen bg-bg2 text-t1 p-6">

        <h1 class="text-2xl font-bold mb-6 text-accent">🔒 Block / Unblock User</h1>

        <!-- Search Form -->
        <form action="{{ route('admin.users.blockForm') }}" method="GET" class="flex gap-3 mb-6">
            <input type="text" name="query" value="{{ request('query') }}"
                placeholder="Enter email, phone, or account number"
                class="px-4 py-2 rounded-xl w-96 bg-bg1 border border-accent text-t1 focus:outline-none" />
            <button class="bg-accent px-4 py-2 rounded-xl text-bg1 font-semibold hover:opacity-90">Search</button>
        </form>

        @if(request('query') && !$user)
            <p class="text-red-500">No user found for your search.</p>
        @endif

        @if($user)
            <div class="bg-bg1 rounded-xl p-6 shadow-md max-w-md">
                <h2 class="text-xl font-semibold mb-2">{{ $user->full_name }}</h2>
                <p>Email: {{ $user->email }}</p>
                <p>Phone: {{ $user->phone_number }}</p>
                <p>Account: {{ $user->account_number }}</p>
                <p>Status: 
                    @if($user->is_blocked)
                        <span class="text-red-500 font-bold">Blocked</span>
                    @else
                        <span class="text-green-500 font-bold">Active</span>
                    @endif
                </p>

                <!-- Block/Unblock Form -->
                <form action="{{ route('admin.users.toggleBlock') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button type="submit" 
                        class="w-full px-4 py-2 rounded-xl font-semibold 
                        {{ $user->is_blocked ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $user->is_blocked ? 'Unblock User' : 'Block User' }}
                    </button>
                </form>
            </div>
        @endif

    </div>
</x-layouts.admin>
