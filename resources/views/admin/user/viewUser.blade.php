<x-layouts.admin>
    <div class="min-h-screen p-6 rounded-2xl shadow-lg
        bg-bg1 text-t1">

        <!-- Back -->
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Title -->
        <div class="w-full text-start -mt-1 p-4">
            <span class="text-2xl font-bold text-accent mb-6">
                👥 All Users
            </span>
        </div>

        <!-- Search -->
        <form method="GET" action="{{ route('viewUser') }}" class="flex mb-6">
            <input type="text"
                name="search"
                placeholder="Search by Email or Phone"
                value="{{ request('search') }}"
                class="flex-1 p-3 rounded-l-lg
                    bg-bg2 border border-accent
                    text-t1 placeholder:text-t3
                    focus:ring-2 focus:ring-accent outline-none">

            <button type="submit"
                class="bg-accent text-bg1 px-5 rounded-r-lg font-semibold
                    hover:opacity-90">
                Search
            </button>
        </form>

        <!-- Flash -->
        @if (session('success'))
            <div class="bg-accent text-bg1 p-3 rounded mb-3 font-semibold">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-3 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl shadow-lg
            bg-bg2 border border-accent">

            <table class="w-full border-collapse">
                <thead class="bg-accent/10 text-accent">
                    <tr>
                        <th class="py-3 px-4 text-left">#</th>
                        <th class="py-3 px-4 text-left">Full Name</th>
                        <th class="py-3 px-4 text-left">Email</th>
                        <th class="py-3 px-4 text-left">Phone</th>
                        <th class="py-3 px-4 text-left">Role</th>
                        <th class="py-3 px-4 text-left">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-accent/20
                            hover:bg-accent/5 transition">

                            <td class="py-3 px-4">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4">{{ $user->full_name }}</td>
                            <td class="py-3 px-4">{{ $user->email }}</td>
                            <td class="py-3 px-4">{{ $user->phone_number }}</td>
                            <td class="py-3 px-4 capitalize">{{ $user->role }}</td>

                            <td class="py-3 px-4 flex gap-2">
                                <a href="{{ route('User.edit', $user->id) }}"
                                    class="bg-accent text-bg1 px-3 py-1 rounded-lg
                                        font-semibold hover:opacity-90">
                                    Edit
                                </a>

                                <button type="button"
                                    onclick="openDeleteModal('{{ $user->id }}', '{{ $user->full_name }}')"
                                    class="bg-red-500 text-white px-3 py-1 rounded-lg
                                        font-semibold hover:bg-red-600">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-t3">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm
        flex items-center justify-center z-50">

        <div class="bg-bg2 border border-accent rounded-2xl p-6 shadow-2xl
            w-[90%] max-w-md text-center text-t1">

            <h2 class="text-xl font-bold mb-2 text-accent">⚠️ Confirm Delete</h2>
            <p id="deleteMessage" class="text-t2 mb-6"></p>

            <div class="flex justify-center gap-4">
                <button onclick="closeDeleteModal()"
                    class="bg-bg3 px-4 py-2 rounded-lg font-semibold">
                    Cancel
                </button>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-red-500 px-4 py-2 rounded-lg font-semibold text-white">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-layouts.admin>

