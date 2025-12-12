<x-layouts.admin>
    <div class="bg-[#0d0d0f] min-h-screen text-white py-10 px-4">

        <div class="max-w-xl mx-auto bg-[#0B141A] p-6 rounded-2xl border border-[#1F2A33]">
            <h2 class="text-xl font-semibold text-[#58a6ff] mb-4">
                Configure Router Settings
            </h2>

            <p class="text-sm text-gray-400 mb-4">
                Select a reseller below to open its WAN configuration panel.
            </p>

            <form id="resellerForm">
                <label class="block mb-2 text-sm font-semibold text-[#9fb1bb]">
                    Select Reseller
                </label>

                <select id="resellerSelect"
                        class="w-full bg-[#141E26] p-3 rounded-xl border border-[#1F2A33] text-white">
                    <option value="">-- Choose Reseller --</option>

                    @foreach ($resellers as $reseller)
                        <option value="{{ route('VoucherSettings.addWanPort', $reseller->id) }}">
                            {{ $reseller->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <button id="openBtn"
                class="mt-5 w-full bg-[#00FFD1] text-black py-2 rounded-xl font-semibold hover:bg-[#0EE6BF] transition">
                Open WAN Settings
            </button>
        </div>
    </div>

    <script>
        document.getElementById('openBtn').addEventListener('click', function () {
            let url = document.getElementById('resellerSelect').value;

            if (!url) {
                alert('Please select a reseller.');
                return;
            }

            window.location.href = url;
        });
    </script>
</x-layouts.admin>
