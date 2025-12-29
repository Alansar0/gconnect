{{-- <x-layouts.admin>
    <div class="max-w-2xl mx-auto p-6 bg-[#101E2B] rounded-2xl">


        <div class=" w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        <div class="w-full text-center -mt-1 p-4">
            <span class="text-2xl font-bold text-[#58a6ff] mb-6">
                New Announcement
            </span>
        </div>

        @if (session('success'))
            <div class="bg-[#00FFD1] text-[#101E2B] p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <form action="{{ route('Snotifystore') }}" method="POST" class="space-y-4">
            @csrf
            <textarea name="message" rows="5" required
                class="w-full p-3 rounded bg-[#0C1621] border border-white/10 text-white" placeholder="Write the announcement...">{{ old('message') }}</textarea>

            <input name="url" type="url" placeholder="Optional URL" value="{{ old('url') }}"
                class="w-full p-3 rounded bg-[#0C1621] border border-white/10 text-white" />

            <select name="send_to" class="w-full p-3 rounded bg-[#0C1621] border border-white/10 text-white">
                <option value="all">All users</option>
                <option value="users">Only users</option>
                <option value="admins">Only admins</option>
            </select>

            <button type="submit" class="w-full py-3 bg-[#00FFD1] text-[#101E2B] font-semibold rounded-lg">Send
                Announcement</button>
        </form>
    </div>
</x-layouts.admin> --}}

<x-layouts.admin>
    <div class="max-w-2xl mx-auto p-6 bg-bg2 text-t1 rounded-2xl font-sans">

        <!-- Back Link -->
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Page Title -->
        <div class="w-full text-center -mt-1 p-4">
            <span class="text-2xl font-bold text-accent mb-6">
                New Announcement
            </span>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-accent text-bg1 p-3 rounded mb-4 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Announcement Form -->
        <form action="{{ route('Snotifystore') }}" method="POST" class="space-y-4">
            @csrf
            <textarea name="message" rows="5" required
                class="w-full p-3 rounded bg-bg3 border border-accent-border text-t1"
                placeholder="Write the announcement...">{{ old('message') }}</textarea>

            <input name="url" type="url" placeholder="Optional URL" value="{{ old('url') }}"
                class="w-full p-3 rounded bg-bg3 border border-accent-border text-t1" />

            <select name="send_to"
                class="w-full p-3 rounded bg-bg3 border border-accent-border text-t1">
                <option value="all">All users</option>
                <option value="users">Only users</option>
                <option value="admins">Only admins</option>
            </select>

            <button type="submit" class="w-full py-3 bg-accent text-bg1 font-semibold rounded-lg hover:opacity-90 transition">
                Send Announcement
            </button>
        </form>
    </div>
</x-layouts.admin>

