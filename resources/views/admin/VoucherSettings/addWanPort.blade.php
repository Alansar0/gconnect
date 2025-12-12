<x-layouts.admin>
    <div class="bg-[#0d0d0f] text-white min-h-screen font-sans py-6 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-[#58a6ff]">Router Settings — {{ $reseller->name }}</h2>
                <a href="{{ url()->previous() }}" class="text-[#8fb9ff] hover:underline">Back</a>
            </div>

            @if(session('success'))
                <div class="bg-green-600/20 text-green-200 p-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-[#141E26] rounded-2xl p-4 border border-[#1F2A33]">
                    <p class="text-sm text-[#9fb1bb]">Active WAN</p>
                    <div class="mt-2 text-white font-medium">{{ strtoupper($liveActiveWan) }}
</
                        div>

                    <div class="mt-4 text-sm text-[#9fb1bb]">Current Counts</div>
                    <div class="mt-2 flex gap-3">
                        <div class="bg-[#0B141A] p-2 rounded-lg flex-1 text-center">    
                            <div class="text-xs text-gray-300">WAN1</div>
                            <div class="text-lg font-bold">{{ $settings->wan1_current_count }}</div>
                            <div class="text-xs text-gray-400">Limit: {{ $settings->wan1_limit }}</div>
                        </div>
                        <div class="bg-[#0B141A] p-2 rounded-lg flex-1 text-center">
                            <div class="text-xs text-gray-300">WAN2</div>
                            <div class="text-lg font-bold">{{ $settings->wan2_current_count }}</div>
                            <div class="text-xs text-gray-400">Limit: {{ $settings->wan2_limit }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#141E26] rounded-2xl p-4 border border-[#1F2A33]">
                    <p class="text-sm text-[#9fb1bb]">Sold-out status</p>
                    
                    <div class="mt-2">
                       

@if(!empty($settings->global_sold_out_until))
    <div class="text-white font-medium">
        Until: {{ $settings->global_sold_out_until->toDateTimeString() }}
    </div>
    <div class="text-xs text-gray-400 mt-1">
        ({{ now()->diffForHumans($settings->global_sold_out_until) }})
    </div>
@else
    <div class="text-sm text-gray-300">Not in sold-out state</div>
@endif

                    </div>

@php
    $waitingCount = \App\Models\Waitlist::where('reseller_id', $reseller->id)
        ->where('status','waiting')
        ->count();

    $nextWait = \App\Models\Waitlist::where('reseller_id', $reseller->id)
        ->where('status','waiting')
        ->orderBy('expected_available_at')
        ->first();
@endphp


<div class="mt-4">
    <div class="text-sm text-[#9fb1bb]">Waitlist</div>
    <div class="text-white font-medium">{{ $waitingCount }} waiting</div>
    @if($nextWait)
        <div class="text-xs text-gray-400 mt-1">Next available: 
            {{ $nextWait->expected_available_at->toDateTimeString() }}
        </div>
    @endif
</div>

                    <div class="mt-4">
                        <form action="{{ route('admin.router-settings.reset', $reseller->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full bg-[#00FFD1] text-black rounded-xl py-2 font-semibold hover:bg-[#00CCA9]">
                                Reset Counters
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Update form -->
            <form action="{{ route('admin.router-settings.update', $reseller->id) }}" method="POST" class="bg-[#0B141A] rounded-2xl p-6 border border-[#1F2A33]">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-[#9fb1bb]">WAN1 Limit</label>
                        <input name="wan1_limit" type="number" min="0" value="{{ old('wan1_limit', $settings->wan1_limit) }}"
                               class="w-full mt-2 p-3 rounded-xl bg-[#141E26] border border-[#1F2A33] text-white"/>
                    </div>

                    <div>
                        <label class="text-sm text-[#9fb1bb]">WAN2 Limit</label>
                        <input name="wan2_limit" type="number" min="0" value="{{ old('wan2_limit', $settings->wan2_limit) }}"
                               class="w-full mt-2 p-3 rounded-xl bg-[#141E26] border border-[#1F2A33] text-white"/>
                    </div>

                    <div>
                        <label class="text-sm text-[#9fb1bb]">Active WAN</label>
                        <select name="active_wan_port" class="w-full mt-2 p-3 rounded-xl bg-[#141E26] border border-[#1F2A33] text-white">
                            <option value="ether1" {{ $settings->active_wan_port=='ether1' ? 'selected' : '' }}>WAN1 (ether1)</option>
                            <option value="ether2" {{ $settings->active_wan_port=='ether2' ? 'selected' : '' }}>WAN2 (ether2)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-[#9fb1bb]">Sold-out Until (optional)</label>
                        <input name="global_sold_out_until" type="datetime-local" value="{{ optional($settings->global_sold_out_until)->format('Y-m-d\TH:i') }}"
                               class="w-full mt-2 p-3 rounded-xl bg-[#141E26] border border-[#1F2A33] text-white"/>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-[#58a6ff] px-4 py-2 rounded-xl font-semibold hover:opacity-95">
                        Save Settings
                    </button>

                    <a href="{{ route('VoucherSettings.addWanPort', $reseller->id) }}" class="ml-auto text-sm text-gray-400 self-center hover:underline">
                        Refresh
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
