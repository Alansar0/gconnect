

<x-layouts.app>
    <div class="min-h-screen bg-[var(--bg-3)] text-[var(--text-1)] flex flex-col items-center font-sans">

        {{-- HEADER --}}
        <header class="fixed top-0 left-0 right-0 z-40 bg-[var(--unique-card-bg)] border-b border-[var(--unique-card-border)] shadow-[0_0_15px_var(--shadow-accent)]">
            <div class="text-center py-4 relative">
                <a href="{{ url()->previous() }}" class="absolute left-6 top-1/2 -translate-y-1/2 text-[var(--accent)] hover:underline flex items-center">
                    <i class="material-icons mr-1 text-[var(--accent)]">arrow_back</i>
                    Back
                </a>
                <h1 class="text-[var(--text-1)] text-xl font-semibold tracking-wide">Makaranta</h1>
            </div>

            {{-- Switcher --}}
            <div class="w-[65vw] mx-auto p-1 flex items-center justify-between bg-[var(--bg-2)] rounded-full border border-[var(--accent)]/50 shadow-[0_0_20px_rgba(0,255,209,0.4)] mb-3 transition-all">
                <button id="btnSauraro"
                        onclick="window.location.href='{{ route('makaranta.darasi', ['course' => $course]) }}'"
                        class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)]
                        {{ request()->routeIs('darasi.sauraro') ? 'bg-[var(--accent)]/30' : 'hover:bg-[var(--accent)]/20' }} transition-all">
                    🎧 Sauraro
                </button>

                <button id="btnKaranta"
                        onclick="window.location.href='{{ route('makaranta.karanta', ['pageId' => $page['page'] ?? 1]) }}'"
                        class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)]
                        {{ request()->routeIs('darasi.karanta') ? 'bg-[var(--accent)]/30' : 'hover:bg-[var(--accent)]/20' }} transition-all">
                    📖 Karanta
                </button>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <section class="flex-grow w-full px-6 py-4 mt-[110px] max-w-3xl">
            <div id="readerWrapper" class="touch-pan-x">
                <div class="bg-[var(--bg-2)] p-6 rounded-2xl shadow-lg border border-[var(--unique-card-border)] mb-6">
                    <h2 class="text-[var(--accent)] text-lg font-bold mb-2">{{ $page['header'] ?? 'Lesson Title' }}</h2>
                    <p id="reader" class="text-[var(--text-1)]/90 leading-relaxed whitespace-pre-line">
                        {{ $page['content'] ?? 'Lesson content goes here.' }}
                    </p>
                </div>
            </div>

            {{-- Trigger Button --}}
            <div class="flex justify-center mt-6">
                <button id="quizBtn"
                        class="bg-[var(--accent)] text-[var(--bg-2)] px-6 py-2 rounded-lg font-semibold hover:scale-105 transition">
                    🧠 Take Quiz
                </button>
            </div>
        </section>

        {{-- QUIZ MODAL --}}
        <div id="quizModal"
             class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center z-50">
            <div class="bg-[var(--unique-card-bg)] rounded-2xl p-6 w-11/12 max-w-md border border-[var(--unique-card-border)] shadow-lg text-[var(--text-1)]">
                <h3 class="text-lg font-semibold text-[var(--accent)] mb-4 text-center">🧠 Quick Quiz</h3>

                @if (!empty($quizzes))
                    @php $randomTwo = collect($quizzes)->shuffle()->take(2); @endphp
                    <form id="quizForm" action="{{ route('quiz.submit', ['pageId' => $page['page']]) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="karanta">
                        @foreach ($randomTwo as $index => $quiz)
                            <div>
                                <p class="mb-2">{{ $index + 1 }}. {{ $quiz['question'] }}</p>
                                @foreach ($quiz['options'] as $i => $opt)
                                    <label class="block">
                                        <input type="radio" name="quiz{{ $index }}" value="{{ $i }}" class="mr-2" required>
                                        {{ $opt }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                        <button type="submit"
                                class="w-full bg-[var(--accent)] text-[var(--bg-2)] py-2 rounded-lg mt-3 font-semibold hover:scale-105 transition">
                            Submit
                        </button>
                    </form>
                @else
                    <p class="text-[var(--text-2)] text-center">No quiz available for this lesson yet.</p>
                @endif
            </div>
        </div>

        {{-- REWARD MODAL --}}
        <div id="rewardModal"
             class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center z-50">
            <div class="bg-[var(--unique-card-bg)] rounded-2xl p-6 w-10/12 max-w-sm border border-[var(--unique-card-border)] text-center">
                <h3 class="text-[var(--accent)] text-xl font-semibold mb-3" id="rewardTitle">🎉 Congratulations!</h3>
                <p class="text-[var(--text-1)]/80 mb-4" id="rewardMessage">You earned ₦50 reward 🎁</p>
                <button id="closeReward"
                        class="bg-[var(--accent)] text-[var(--bg-2)] px-6 py-2 rounded-lg font-semibold hover:scale-105 transition">OK</button>
            </div>
        </div>
 </div>
        {{-- SCRIPTS --}}

    {{-- <script>
            document.addEventListener('DOMContentLoaded', () => {
                            const quizForm = document.getElementById('quizForm');
                            const quizModal = document.getElementById('quizModal');
                            const rewardModal = document.getElementById('rewardModal');
                            const closeReward = document.getElementById('closeReward');
                            const rewardTitle = document.getElementById('rewardTitle') || null;
                            const rewardMessage = document.getElementById('rewardMessage') || null;

                            // Keys for local storage per page - adapt page values accordingly
                            const pageIdentifier = '{{$page["page"] ?? ($file ?? "unknown")}}';
                            const quizLocalKey = 'quiz_completed_' + pageIdentifier;
                            let quizCompleted = localStorage.getItem(quizLocalKey) === 'true';

                            if (quizForm) {
                                quizForm.addEventListener('submit', async (e) => {
                                    e.preventDefault();
                                    const formData = new FormData(quizForm);

                                    try {
                                        const res = await fetch(quizForm.action, {
                                            method: 'POST',
                                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                            body: formData
                                        });

                                        const data = await res.json();

                                        // close quiz modal
                                        if (quizModal) quizModal.classList.add('hidden');

                                        if (data.status === 'success') {
                                            // mark quiz completed locally (so UI reflects it)
                                            quizCompleted = true;
                                            localStorage.setItem(quizLocalKey, 'true');

                                            if(rewardTitle) rewardTitle.textContent = '🎉 Congratulations!';
                                            if(rewardMessage) rewardMessage.textContent = data.message || 'You earned a reward';

                                            if (rewardModal) rewardModal.classList.remove('hidden');

                                            // Optionally: update any wallet UI in page, e.g. display new balances
                                            console.log('Wallet updated:', data);
                                        } else {
                                            // incorrect or server-side error
                                            if(rewardTitle) rewardTitle.textContent = '❌ Try Again';
                                            if(rewardMessage) rewardMessage.textContent = data.message || 'Incorrect answers. Please try again.';
                                            if (rewardModal) rewardModal.classList.remove('hidden');
                                        }
                                    } catch (err) {
                                        console.error('Quiz submission failed', err);
                                        if (rewardTitle) rewardTitle.textContent = '❌ Error';
                                        if (rewardMessage) rewardMessage.textContent = 'Network error. Please try again.';
                                        if (rewardModal) rewardModal.classList.remove('hidden');
                                    }
                                });
                            }

                            if (closeReward) {
                                closeReward.addEventListener('click', () => {
                                    if (rewardModal) rewardModal.classList.add('hidden');
                                });
                            }

                            // Optionally, disable "Take Quiz" if already completed
                            const takeButtons = document.querySelectorAll('#quizBtn');
                            if (quizCompleted) {
                                takeButtons.forEach(b => {
                                    b.classList.add('opacity-60', 'cursor-not-allowed');
                                    b.disabled = true;
                                    b.title = 'Quiz already completed (local)';
                                });
                            }
                        }); 
    </script> --}}

    <script>
        
        document.addEventListener('DOMContentLoaded', () => {
                    const quizBtn = document.getElementById('quizBtn');
                    const quizModal = document.getElementById('quizModal');
                    const rewardModal = document.getElementById('rewardModal');
                    const closeReward = document.getElementById('closeReward');
                    const quizForm = document.getElementById('quizForm');

                    if (quizBtn) {
                        quizBtn.addEventListener('click', () => quizModal.classList.remove('hidden'));
                    }

                    if (quizForm) {
                        quizForm.addEventListener('submit', async (e) => {
                            e.preventDefault();
                            try {
                                const res = await fetch(quizForm.action, {
                                    method: 'POST',
                                    body: new FormData(quizForm),
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                const result = await res.json();
                                quizModal.classList.add('hidden');

                                const rewardTitle = document.getElementById('rewardTitle');
                                const rewardMessage = document.getElementById('rewardMessage');

                                if (result.status === 'success') {
                                    rewardTitle.textContent = '🎉 Congratulations!';
                                    rewardMessage.textContent = result.message || 'You earned ₦50 reward 🎁';
                                } else {
                                    rewardTitle.textContent = '❌ Try Again';
                                    rewardMessage.textContent = result.message || 'Incorrect answers. Please try again.';
                                }

                                rewardModal.classList.remove('hidden');
                            } catch (err) {
                                console.error('Quiz submission failed:', err);
                                alert('Something went wrong. Please try again.');
                            }
                        });
                    }

                    if (closeReward) {
                        closeReward.addEventListener('click', () => rewardModal.classList.add('hidden'));
                    }

                    setTimeout(() => {
                        if (quizModal) quizModal.classList.remove('hidden');
                    }, 45000);
                });
    </script>

</x-layouts.app>

