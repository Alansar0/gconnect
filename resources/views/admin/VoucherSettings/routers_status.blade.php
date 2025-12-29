<x-layouts.admin>

    <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="flex items-center justify-inline mb-6">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center mb-8">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a> 
            <h2 class="text-xl font-semibold text-accent ml-10 mt-14"> Online Routers</h2>
        </div>

        <div class="grid gap-3">
        @forelse($resellers as $reseller)
            <div class="p-4 rounded-xl bg-bg7 border my-accent flex justify-between">
                <span>{{ $reseller->name }}</span>
                <span class="text-green-400 text-sm">
                    Online
                </span>
            </div>
        @empty
            <p class="text-t3">No routers online.</p>
        @endforelse
    </div>
    </div>
</x-layouts.admin>
