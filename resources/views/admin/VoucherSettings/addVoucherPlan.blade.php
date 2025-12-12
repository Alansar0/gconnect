<x-layouts.admin>
    <div class="min-h-screen bg-[#0B141A] text-white p-8">
        <div class=" w-full flex justify-start mt-4 mb-4">
            <a onclick="window.history.back()" class="text-[#58a6ff] hover:underline flex items-center">
    <i class="material-icons mr-1">arrow_back</i> Back
</a>
        </div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#58a6ff]">Voucher Profiles</h1>
            <button onclick="toggleModal()"
                class="bg-[#00FFD1] text-black font-bold px-4 py-2 rounded-lg hover:bg-[#00CCA9]">+ Add Profile</button>
        </div>

        @if (session('success'))
            <div class="bg-green-600/30 text-green-200 p-4 rounded-xl mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-[#1F2A33]">
            <table class="min-w-full text-left border-collapse">
                <thead class="bg-[#111B25] text-gray-400 text-sm uppercase">
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
                        <tr class="border-b border-[#1F2A33] hover:bg-[#141E26] transition">
                            <td class="px-4 py-3">{{ $p->name }}</td>
                            <td class="px-4 py-3">{{ $p->mikrotik_profile }}</td>
                            <td class="px-4 py-3">{{ $p->time_minutes }}</td>
                            <td class="px-4 py-3">₦{{ number_format($p->price, 2) }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $p->status === 'active' ? 'bg-green-600/40 text-green-300' : 'bg-gray-700 text-gray-400' }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form id="delete-form-{{ $p->id }}" method="POST"
                                    action="{{ route('admin.voucher_profiles.destroy', $p->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $p->id }})"
                                        class="text-red-400 hover:text-red-300 font-semibold transition">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">No profiles yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Add Profile Modal -->
        <div id="modal" class="hidden fixed inset-0 bg-black/70 flex justify-center items-center z-50">
            <div class="bg-[#111B25] rounded-2xl p-6 w-[90%] max-w-md">
                <h2 class="text-xl font-semibold mb-4 text-[#00FFD1]">Add New Profile</h2>
                <form method="POST" action="{{ route('admin.voucher_profiles.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1">Name</label>
                        <input name="name" class="w-full bg-[#1B2632] border border-[#233044] rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">MikroTik Profile Name</label>
                        <input name="mikrotik_profile"
                            class="w-full bg-[#1B2632] border border-[#233044] rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Time (minutes)</label>
                        <input type="number" name="time_minutes"
                            class="w-full bg-[#1B2632] border border-[#233044] rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Price (₦)</label>
                        <input type="number" step="0.01" name="price"
                            class="w-full bg-[#1B2632] border border-[#233044] rounded-lg px-3 py-2">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="toggleModal()"
                            class="px-4 py-2 bg-gray-600/40 rounded-lg hover:bg-gray-600/60">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#00FFD1] text-black font-semibold rounded-lg hover:bg-[#00CCA9]">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function toggleModal() {
                document.getElementById('modal').classList.toggle('hidden');
            }
        </script>
    </div>

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
                cancelButtonText: 'Cancel',
                background: '#0B141A',
                color: '#fff',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-[#1F2A33]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>

</x-layouts.admin>
