

<x-layouts.admin>
<div class="min-h-screen p-8 bg-bg1 dark:bg-bg1 text-t1 dark:text-t1">

    <!-- Back -->
    <div class="w-full flex justify-start mt-4 mb-4">
        <a onclick="window.history.back()" class="text-accent hover:underline flex items-center">
            <i class="material-icons mr-1 text-accent">arrow_back</i> Back
        </a>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-accent">Voucher Profiles</h1>
        <button onclick="toggleModal()"
            class="bg-accent text-bg1 font-bold px-4 py-2 rounded-lg hover:opacity-80 transition">
            + Add Profile
        </button>
    </div>

    <!-- Success -->
    @if (session('success'))
        <div class="bg-green-500/20 text-green-600 dark:text-green-400 p-4 rounded-xl mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto rounded-xl border border-accent/20">
        <table class="min-w-full text-left border-collapse">
            <thead class="bg-bg3 dark:bg-bg3 text-t3 dark:text-t3 text-sm uppercase">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">MikroTik Profile</th>
                    <th class="px-4 py-3">Time (min)</th>
                    <th class="px-4 py-3">Price (₦)</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($profiles as $p)
                    <tr class="border-b border-accent/10 hover:bg-bg3/60 dark:hover:bg-bg3 transition">
                        <td class="px-4 py-3">{{ $p->name }}</td>
                        <td class="px-4 py-3">{{ $p->mikrotik_profile }}</td>
                        <td class="px-4 py-3">{{ $p->time_minutes }}</td>
                        <td class="px-4 py-3">₦{{ number_format($p->price, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $p->status === 'active'
                                    ? 'bg-green-500/20 text-green-600 dark:text-green-400'
                                    : 'bg-gray-500/20 text-gray-500 dark:text-gray-400' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form id="delete-form-{{ $p->id }}" method="POST"
                                action="{{ route('admin.voucher_profiles.destroy', $p->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $p->id }})"
                                    class="text-red-500 hover:text-red-400 font-semibold transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-t3 dark:text-t3">
                            No profiles yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="modal" class="hidden fixed inset-0 bg-black/70 flex justify-center items-center z-50">
        <div class="bg-bg2 dark:bg-bg2 rounded-2xl p-6 w-[90%] max-w-md border border-accent/20">
            <h2 class="text-xl font-semibold mb-4 text-accent">Add New Profile</h2>

            <form method="POST" action="{{ route('admin.voucher_profiles.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Name</label>
                    <input name="name"
                        class="w-full bg-bg3 dark:bg-bg3 border border-accent/20 rounded-lg px-3 py-2 text-t1">
                </div>

                <div>
                    <label class="block text-sm mb-1">MikroTik Profile Name</label>
                    <input name="mikrotik_profile"
                        class="w-full bg-bg3 dark:bg-bg3 border border-accent/20 rounded-lg px-3 py-2 text-t1">
                </div>

                <div>
                    <label class="block text-sm mb-1">Time (minutes)</label>
                    <input type="number" name="time_minutes"
                        class="w-full bg-bg3 dark:bg-bg3 border border-accent/20 rounded-lg px-3 py-2 text-t1">
                </div>

                <div>
                    <label class="block text-sm mb-1">Price (₦)</label>
                    <input type="number" step="0.01" name="price"
                        class="w-full bg-bg3 dark:bg-bg3 border border-accent/20 rounded-lg px-3 py-2 text-t1">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="toggleModal()"
                        class="px-4 py-2 bg-gray-500/30 rounded-lg hover:bg-gray-500/50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-accent text-bg1 font-semibold rounded-lg hover:opacity-80 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function toggleModal() {
        document.getElementById('modal').classList.toggle('hidden');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action will permanently delete the voucher profile.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#00FFD1',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            background: 'var(--bg1)',
            color: 'var(--text1)'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
</x-layouts.admin>
