<x-layouts.admin>
    <div class="min-h-screen py-10 px-4 bg-bg1 text-t1 font-sans">
        <div class="max-w-xl mx-auto p-6 rounded-2xl border border-accent-border bg-bg2">

            <!-- Header -->
            <h2 class="text-xl font-semibold text-accent mb-4">
                Configure Router Settings
            </h2>

            <!-- Description -->
            <p class="text-sm text-t3 mb-4">
                Select a reseller below to open its WAN configuration panel.
            </p>

            <!-- Reseller Form -->
            <form id="resellerForm">
                <label class="block mb-2 text-sm font-semibold text-t3">
                    Select Reseller
                </label>

                <select id="resellerSelect"
                        class="w-full bg-bg3 p-3 rounded-xl border border-accent-border text-t1">
                    <option value="">-- Choose Reseller --</option>

                    @foreach ($resellers as $reseller)
                        <option value="{{ route('VoucherSettings.addWanPort', $reseller->id) }}">
                            {{ $reseller->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <!-- Open Button -->
            <button id="openBtn"
                class="mt-5 w-full bg-accent text-t1 py-2 rounded-xl font-semibold hover:bg-accent-soft transition">
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
