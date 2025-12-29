
<x-layouts.app>
    <div class="min-h-screen p-6 bg-bg1 text-t1 font-sans">

        <!-- Back Link -->
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Page Title -->
        <h1 class="text-2xl font-bold mb-6 text-accent">Support Topics & Contact Setup</h1>

        <!-- Add / Edit Topic -->
        <div class="bg-bg2 p-6 rounded-xl shadow mb-8 border border-accent-border">
            <h2 class="text-lg font-semibold mb-3 text-accent">Add Support Topic</h2>

            <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-1 text-t3">Topic Title</label>
                    <input type="text" name="title"
                           class="w-full p-2 rounded bg-bg3 border border-accent-border text-t1" required>
                </div>

                <div>
                    <label class="block mb-1 text-t3">WhatsApp Contact Link</label>
                    <input type="text" name="whatsapp_link" placeholder="https://wa.me/234XXXXXXXXXX"
                           class="w-full p-2 rounded bg-bg3 border border-accent-border text-t1" required>
                </div>

                <button class="bg-accent hover:bg-accent-soft px-4 py-2 rounded text-t1 font-medium transition">
                    Add Topic
                </button>
            </form>
        </div>

        <!-- Add Sub Question -->
        <div class="bg-bg2 p-6 rounded-xl shadow mb-8 border border-accent-border">
            <h2 class="text-lg font-semibold mb-3 text-accent">Add Sub-Question</h2>

            <form action="{{ route('settings.sub.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-1 text-t3">Select Topic</label>
                    <select name="support_topic_id"
                            class="w-full p-2 rounded bg-bg3 border border-accent-border text-t1" required>
                        <option value="">-- Choose Topic --</option>
                        @foreach ($topics as $topic)
                            <option value="{{ $topic->id }}">{{ $topic->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-t3">Question Text</label>
                    <input type="text" name="question"
                           class="w-full p-2 rounded bg-bg3 border border-accent-border text-t1" required>
                </div>

                <button class="bg-accent2 hover:bg-accent-soft px-4 py-2 rounded text-t1 font-medium transition">
                    Add Sub-Question
                </button>
            </form>
        </div>

        <!-- Existing Topics -->
        <div class="max-w-4xl mx-auto">
            <h2 class="text-xl mb-4 font-semibold text-accent">Existing Topics</h2>

            @foreach ($topics as $topic)
                <div class="bg-bg3 p-5 mb-4 rounded-xl border border-accent-border">
                    <h3 class="text-lg font-bold mb-2 text-accent">{{ $topic->title }}</h3>
                    <p class="text-sm text-t3 mb-2">WhatsApp: {{ $topic->whatsapp_link }}</p>

                    <ul class="list-disc pl-6 text-sm text-t3">
                        @foreach ($topic->subQuestions as $q)
                            <li>{{ $q->question }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

    </div>
</x-layouts.app>
