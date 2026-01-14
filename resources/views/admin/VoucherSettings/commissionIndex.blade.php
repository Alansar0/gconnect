<x-layouts.admin>
    <div class="container mx-auto p-6 bg-bg1 rounded-2xl text-t1">

        <h1 class="text-2xl font-semibold mb-6 text-accent">
            Resellers
        </h1>

        @if(session('success'))
            <div class="mb-4 p-3 bg-accent text-bg1 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4">
            @foreach($resellers as $reseller)
                <div class="p-4 rounded-xl bg-bg2 border border-accent-border flex justify-between items-center">
                    <div>
                        <div class="font-semibold">
                            {{ $reseller->name ?? 'Reseller #' . $reseller->id }}
                        </div>
                        <div class="text-sm text-t3">
                            Commission: {{ $reseller->commission_percent }}%
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.Commission.edit', $reseller) }}"
                       class="px-4 py-2 bg-accent text-bg1 rounded-xl font-semibold">
                        Edit
                    </a>
                </div>
            @endforeach
        </div>

    </div>
</x-layouts.admin>
