

 <x-layouts.admin>
<div class="min-h-screen font-sans py-6 px-4 voucher-page text-t1">
    <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="flex items-center justify-inline mb-6">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center mb-8">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a> 
            <h2 class="text-xl font-semibold text-accent -ml-6 mt-14">Router for {{ $reseller->name }}</h2>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-600/20 text-green-200 dark:text-green-300 p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @php
    // Live Waitlist & next available
    $waitingUsers = \App\Models\Waitlist::where('reseller_id', $reseller->id)
        ->where('status', 'waiting')
        ->orderBy('expected_available_at')
        ->get();

    $waitingCount = $waitingUsers->count();
    $nextWait = $waitingUsers->first();

    // Yesterday snapshot
    $yesterdaySnapshot = \App\Models\WaitlistDailySnapshot::where(
            'reseller_id', $reseller->id
        )
        ->where('snapshot_date', \Carbon\Carbon::yesterday()->toDateString())
        ->first();

    // Last 7 days snapshot for mini graph
    $weeklySnapshots = \App\Models\WaitlistDailySnapshot::where(
            'reseller_id', $reseller->id
        )
        ->whereBetween('snapshot_date', [
            \Carbon\Carbon::now()->subDays(7),
            \Carbon\Carbon::yesterday()
        ])
        ->orderBy('snapshot_date')
        ->get();

    $graphLabels = $weeklySnapshots
        ->pluck('snapshot_date')
        ->map(fn ($d) => \Carbon\Carbon::parse($d)->format('M d'))
        ->toArray();

    $graphData = $weeklySnapshots
        ->pluck('waiting_count')
        ->toArray();
@endphp

        <!-- WAN + Waitlist + Snapshot -->
        <div class="grid grid-cols-2 gap-4 mb-6">

            <!-- Active WAN & Current Counts -->
            <div class="rounded-2xl p-4 border my-accent bg-bg6">
                <p class="text-sm text-t3">Active WAN</p>
                <div class="mt-2 font-medium text-t1">{{ strtoupper($liveActiveWan) }}</div>

                <div class="mt-4 text-sm text-t3">Current Counts</div>
                <div class="mt-2 flex gap-3">
                    <div class="p-2 rounded-lg flex-1 text-center bg-bg7">
                        <div class="text-xs text-t3">WAN1</div>
                        <div class="text-lg font-bold text-t1">{{ $settings->wan1_current_count }}</div>
                        <div class="text-xs text-t3/70">Limit: {{ $settings->wan1_limit }}</div>
                    </div>
                    <div class="p-2 rounded-lg flex-1 text-center bg-bg7">
                        <div class="text-xs text-t3">WAN2</div>
                        <div class="text-lg font-bold text-t1">{{ $settings->wan2_current_count }}</div>
                        <div class="text-xs text-t3/70">Limit: {{ $settings->wan2_limit }}</div>
                    </div>
                </div>
            </div>

            <!-- Live Waitlist -->
            <div class="rounded-2xl p-4 border my-accent bg-bg6">
                <div class="text-sm text-t3">Waitlist</div>
                <div class="font-medium text-t1">{{ $waitingCount }} waiting</div>
                @if($nextWait)
                    <div class="text-xs text-t3/70 mt-1">
                        Next available: {{ $nextWait->expected_available_at->toDateTimeString() }}
                    </div>
                @endif

                <div class="mt-4">
                    <form action="{{ route('admin.router-settings.reset', $reseller->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full bg-accent text-t1 rounded-xl py-2 font-semibold hover:opacity-90">
                            Reset Counters
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Snapshot + Mini Graph -->
        @if($yesterdaySnapshot)
        <div class="rounded-2xl p-4 border my-accent bg-bg6 mb-6">
            <div class="text-sm text-t3 mb-2">Yesterday's Waitlist Snapshot</div>
            <div class="text-t1 mb-2">
                Total Waiting: {{ $yesterdaySnapshot->waiting_count }}<br>
                Waiting 24h+: {{ $yesterdaySnapshot->waiting_24h_plus }}<br>
                Waiting 48h+: {{ $yesterdaySnapshot->waiting_48h_plus }}<br>
                Waiting 72h+: {{ $yesterdaySnapshot->waiting_72h_plus }}
            </div>

            <canvas id="waitlistGraph" class="w-full h-24"></canvas>
        </div>
        @endif

        <!-- Update Form -->
        <form action="{{ route('admin.router-settings.update', $reseller->id) }}" method="POST"
            class="rounded-2xl p-6 border my-accent bg-bg7">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-t3">WAN1 Limit</label>
                    <input name="wan1_limit" type="number" min="0"
                           value="{{ old('wan1_limit', $settings->wan1_limit) }}"
                           class="w-full mt-2 p-3 rounded-xl bg-bg6 border my-accent text-t1"/>
                </div>

                <div>
                    <label class="text-sm text-t3">WAN2 Limit</label>
                    <input name="wan2_limit" type="number" min="0"
                           value="{{ old('wan2_limit', $settings->wan2_limit) }}"
                           class="w-full mt-2 p-3 rounded-xl bg-bg6 border my-accent text-t1"/>
                </div>

                <div>
                    <label class="text-sm text-t3">Active WAN</label>
                    <select name="active_wan_port"
                            class="w-full mt-2 p-3 rounded-xl bg-bg6 border my-accent text-t1">
                        <option value="ether1" {{ $settings->active_wan_port=='ether1' ? 'selected' : '' }}>WAN1 (ether1)</option>
                        <option value="ether2" {{ $settings->active_wan_port=='ether2' ? 'selected' : '' }}>WAN2 (ether2)</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="bg-accent px-4 py-2 rounded-xl font-semibold text-t1 hover:opacity-95">
                    Save Settings
                </button>
                <a href="{{ route('VoucherSettings.addWanPort', $reseller->id) }}"
                   class="ml-auto text-sm text-t3 self-center hover:underline">
                    Refresh
                </a>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('waitlistGraph');
    if(ctx){
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($graphLabels) !!},
                datasets: [{
                    label: 'Waiting Users',
                    data: {!! json_encode($graphData) !!},
                    borderColor: '#58a6ff',
                    backgroundColor: 'rgba(88, 166, 255, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#f0f6fc', font: { size: 10 } }, grid: { display: false } },
                    y: { ticks: { color: '#f0f6fc', font: { size: 10 } }, grid: { color: '#233044' } }
                }
            }
        });
    }
</script>
</x-layouts.admin>
