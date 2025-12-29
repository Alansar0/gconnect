<x-layouts.admin>
    <div class="container mx-auto p-6 bg-bg1 text-t1 font-sans rounded-2xl max-w-2xl">

        <!-- Back -->
        <div class="w-full flex justify-start mb-6">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Header -->
        <h1 class="text-2xl font-bold text-accent mb-6">Emergency Mode Settings</h1>

        <!-- Current State -->
        {{-- <div class="mb-6 p-4 rounded-xl bg-bg2 border border-accent-border">
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-semibold text-t1">Current App State:</span>
                    @if(cache('emergency_mode'))

                        <span class="text-red-500 font-bold">EMERGENCY ACTIVE</span>
                    @else
                        <span class="text-green-500 font-bold">Normal</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.toggleEmergency') }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-xl font-semibold 
                               {{ config('app.emergency_mode') ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}
                               text-bg1 transition">
                        {{ config('app.emergency_mode') ? 'Deactivate Emergency' : 'Activate Emergency' }}
                    </button>
                </form>
            </div>
        </div> --}}
        <!-- Current State -->
<div class="mb-6 p-4 rounded-xl bg-bg2 border border-accent-border">
    <div class="flex justify-between items-center">

        <div>
            <span class="font-semibold text-t1">Current App State:</span>

            @if(cache('emergency_mode'))
                <span class="ml-2 text-red-500 font-bold">
                    EMERGENCY ACTIVE
                </span>
            @else
                <span class="ml-2 text-green-500 font-bold">
                    Normal
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('toggleE') }}">
            @csrf

            <button
                type="submit"
                class="px-4 py-2 rounded-xl font-semibold text-bg1 transition
                    {{ cache('emergency_mode')
                        ? 'bg-green-600 hover:bg-green-700'
                        : 'bg-red-600 hover:bg-red-700' }}"
            >
                {{ cache('emergency_mode')
                    ? 'Disable Emergency'
                    : 'Enable Emergency' }}
            </button>
        </form>

    </div>
</div>


        <!-- Info / Warning -->
        <div class="p-4 bg-bg3 border border-accent-border rounded-xl text-t3 mb-6">
            <p>
                Activating emergency mode will put the entire application into **safe mode**:
                <ul class="list-disc pl-5 mt-2">
                    <li>All user actions will be temporarily disabled.</li>
                    <li>New transactions, logins, and operations will be blocked.</li>
                    <li>Only admin users can deactivate emergency mode.</li>
                </ul>
            </p>
        </div>

        <!-- Optional Notes -->
        <form method="POST" action="{{ route('admin.settings.logEmergency') }}">
            @csrf
            <label class="block mb-2 text-t3">Optional Note / Reason</label>
            <textarea name="note" rows="3"
                      class="w-full p-3 rounded-xl bg-bg3 border border-accent-border text-t1"
                      placeholder="Describe why emergency mode is activated..."></textarea>

            <button type="submit" class="mt-4 w-full py-2 bg-accent text-bg1 font-semibold rounded-xl hover:bg-accent-soft transition">
                Save Note
            </button>
        </form>

    </div>
</x-layouts.admin>
