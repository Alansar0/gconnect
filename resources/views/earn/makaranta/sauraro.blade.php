    <x-layouts.app>
        <div class="min-h-screen flex flex-col items-center justify-center bg-bg1 text-text px-4 py-10">

            <!-- Back Button -->
            <a href="{{ route('makaranta.darasi', ['course' => $course]) }}"
                class="absolute left-6 top-6 text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i>
                Back
            </a>

            <!-- Audio Info -->
            <div class="text-center mb-6">
                <h2 class="text-accent text-2xl font-semibold">{{ $displayFile }}</h2>
                <p class="text-text-soft text-sm mt-1">{{ $displayName }}</p>
            </div>

            <!-- Circular Artwork with Progress -->
            <div
                class="relative w-64 h-64 rounded-full bg-gradient-to-br from-bg2 to-bg3 flex items-center justify-center shadow-[0_0_40px_var(--accent-glow)]">
                <img src="{{ Vite::asset('resources/images/kurakurai100.png') }}"
                    alt="Surah Artwork"
                    class="w-52 h-52 rounded-full object-cover opacity-80 transition-transform duration-200" />

                <!-- Circular Progress -->
                <svg class="absolute top-0 left-0 w-full h-full" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="48" stroke="var(--accent)" stroke-width="1.6" fill="none"
                        opacity="0.25" />
                    <circle id="progressCircle" cx="50" cy="50" r="48"
                        stroke="var(--accent)" stroke-width="2.5"
                        fill="none" stroke-dasharray="301.59" stroke-dashoffset="301.59"
                        stroke-linecap="round" class="transition-all duration-200 ease-linear" />
                </svg>

                <!-- Play/Pause Button -->
                <button id="playBtn"
                    class="absolute bottom-6 right-6 w-16 h-16 bg-accent text-bg1 rounded-full flex items-center justify-center shadow-[0_0_25px_var(--accent-glow)] hover:scale-110 transition-transform duration-200">
                    <i id="playIcon" class="fas fa-play text-2xl"></i>
                </button>
            </div>

            <!-- Track Info -->
            <div class="text-center mt-6">
                <p class="text-lg font-medium text-text">kura kuari 100 Acikin Sallarmu</p>
                <p class="text-text-soft text-sm mt-1">Zakusamu Grabasa Voucher kyauta</p>
                <p class="text-text-soft text-sm mt-1">Idan ka Saurari karatu</p>
            </div>

            <!-- Progress Bar -->
            <div class="w-full max-w-md mt-8">
                <input id="seekBar" type="range" min="0" value="0"
                    class="w-full accent-accent h-2 rounded-lg cursor-pointer bg-bg3 transition-colors duration-200" />
                <div class="flex justify-between text-xs text-text-soft mt-1">
                    <span id="currentTime">0:00</span>
                    <span id="totalTime">0:00</span>
                </div>
            </div>

            <!-- Control Buttons -->
            <div class="flex justify-center items-center gap-20 mt-2">
                <div class="flex flex-col items-center">
                    <button id="downloadBtn" class="text-accent hover:scale-110 transition-transform duration-200">
                        <i class="fas fa-download text-3xl"></i>
                    </button>
                    <span class="text-xs mt-1 text-text-soft">Download</span>
                </div>

                <div class="flex flex-col items-center">
                    <button id="quizBtn" class="text-accent hover:scale-110 transition-transform duration-200">
                        <i class="fas fa-list-check text-3xl"></i>
                    </button>
                    <span class="text-xs mt-1 text-text-soft">Take Quiz</span>
                </div>
            </div>

            <audio id="audioPlayer" src="{{ asset($path) }}"></audio>
        </div>

        <!-- Quiz Modal -->
        <div id="quizModal"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center z-50">
            <div class="bg-bg2 rounded-2xl p-6 w-11/12 max-w-md border border-border shadow-lg text-text">
                <h3 class="text-lg font-semibold text-accent mb-4 text-center">Quick Quiz</h3>

                @if (!empty($quizzes))
                    @php $randomTwo = collect($quizzes)->shuffle()->take(2); @endphp
                    <form id="quizForm" action="{{ route('quiz.submit', ['pageId' => $file]) }}" method="POST"
                        class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="sauraro">

                        @foreach ($randomTwo as $index => $quiz)
                            <div>
                                <p class="mb-2">{{ $index + 1 }}. {{ $quiz['question'] }}</p>
                                @php
                                    // Shuffle options and map correct
                                    $options = $quiz['options'];
                                    $correctIndex = $quiz['correct'];

                                    $shuffledOptions = collect($options)
                                        ->map(function ($option, $i) use ($correctIndex) {
                                            return [
                                                'text' => $option,
                                                'is_correct' => ($i == $correctIndex) // true if correct
                                            ];
                                        })
                                        ->shuffle()
                                        ->values();
                                @endphp

                                @foreach ($shuffledOptions as $i => $opt)
                                    <label class="block">
                                        <input type="radio" 
                                            name="quiz{{ $index }}" 
                                            value="{{ $opt['is_correct'] ? 'correct' : 'wrong' }}"
                                            class="mr-2" 
                                            required>
                                        {{ $opt['text'] }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach

                        <button type="submit"
                            class="w-full bg-accent text-bg1 py-2 rounded-lg mt-3 font-semibold hover:scale-105 transition-transform duration-200">
                            Submit Answers
                        </button>
                    </form>
                @else
                    <p class="text-text-soft text-center">No quiz available.</p>
                @endif
            </div>
        </div>

        <!-- Reward Modal -->
        <div id="rewardModal"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center z-50">
            <div class="bg-bg2 rounded-2xl p-6 w-10/12 max-w-sm border border-border text-center text-text">
                <h3 class="text-accent text-xl font-semibold mb-3">Congratulations!</h3>
                <p id="rewardMessage" class="text-text-soft mb-4"></p>
                {{-- <p class="text-text-soft mb-4">You earned ₦{{ number_format($reward->cashback_amount, 2) }} Cashback and 1 Voucher</p> --}}
                <button id="closeReward"
                    class="bg-accent text-bg1 px-6 py-2 rounded-lg font-semibold hover:scale-105 transition-transform duration-200">OK</button>
            </div>
        </div>

    <!-- Warning Modal -->
    <div id="warningModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center z-50">
        <div class="bg-bg2 rounded-2xl p-6 w-11/12 max-w-md border border-border shadow-lg text-text text-center">
            <p id="warningMessage" class="text-text-soft mb-4 text-lg"></p>
            <button id="closeWarning"
                class="bg-accent text-bg1 px-6 py-2 rounded-lg font-semibold hover:scale-105 transition-transform duration-200">
                OK
            </button>
        </div>
    </div>



    <script>
            /* =========================
            ELEMENTS
            ========================= */
            const audio = document.getElementById('audioPlayer');
            const playBtn = document.getElementById('playBtn');
            const playIcon = document.getElementById('playIcon');
            const progressCircle = document.getElementById('progressCircle');
            const seekBar = document.getElementById('seekBar');
            const currentTime = document.getElementById('currentTime');
            const totalTime = document.getElementById('totalTime');
            const downloadBtn = document.getElementById('downloadBtn');
            const quizBtn = document.getElementById('quizBtn');

            const quizModal = document.getElementById('quizModal');
            const rewardModal = document.getElementById('rewardModal');
            const closeReward = document.getElementById('closeReward');

            const warningModal = document.getElementById('warningModal');
            const warningMessage = document.getElementById('warningMessage');
            const closeWarning = document.getElementById('closeWarning');

            /* =========================
            PROGRESS CONSTANTS
            ========================= */
            const CIRCLE_RADIUS = 48;
            const CIRCUMFERENCE = 2 * Math.PI * CIRCLE_RADIUS;

            /* =========================
            STORAGE KEYS
            ========================= */
            const pageKey = 'audioPage_{{ $file }}';
            const progressKey = pageKey + '_progress';
            const finishedKey = pageKey + '_finished';
            const quizKey = pageKey + '_quizCompleted';

            /* =========================
            STATE
            ========================= */
            let isSeeking = false;
            let lastSeekValue = 0;
            let audioFinished = localStorage.getItem(finishedKey) === 'true';
            let quizCompleted = localStorage.getItem(quizKey) === 'true';
            let blockedSeek = false;


            /* =========================
            RESTORE PROGRESS
            ========================= */
            const savedTime = localStorage.getItem(progressKey);
            if (savedTime) {
                audio.currentTime = parseFloat(savedTime);
                lastSeekValue = audio.currentTime;
            }

            /* =========================
            HELPERS
            ========================= */
            function showWarning(msg) {
                warningMessage.textContent = msg;
                warningModal.classList.remove('hidden');
            }

            closeWarning.addEventListener('click', () => {
                warningModal.classList.add('hidden');
            });

            function formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
            }

            /* =========================
            PLAY / PAUSE
            ========================= */
            playBtn.addEventListener('click', () => {
                if (audio.paused) {
                    audio.play();
                    playIcon.classList.replace('fa-play', 'fa-pause');
                } else {
                    audio.pause();
                    playIcon.classList.replace('fa-pause', 'fa-play');
                }
            });

            /* =========================
            METADATA
            ========================= */
            audio.addEventListener('loadedmetadata', () => {
                seekBar.max = audio.duration;
                totalTime.textContent = formatTime(audio.duration);
                progressCircle.style.strokeDasharray = CIRCUMFERENCE;
                progressCircle.style.strokeDashoffset = CIRCUMFERENCE;
            });

            /* =========================
            SEEK CONTROL
            ========================= */
            seekBar.addEventListener('mousedown', () => isSeeking = true);
            seekBar.addEventListener('touchstart', () => isSeeking = true);


            seekBar.addEventListener('input', e => {
                                const seekTo = Number(e.target.value);

                                // 🚫 FORWARD SEEK BLOCK
                                if (!audioFinished && seekTo > lastSeekValue + 0.2) {
                                    blockedSeek = true;

                                    // Pause audio immediately
                                    audio.pause();
                                    playIcon.classList.replace('fa-pause', 'fa-play');

                                    // Restore slider position
                                    seekBar.value = lastSeekValue;
                                    currentTime.textContent = formatTime(lastSeekValue);

                                    showWarning("You must listen to the audio completely before skipping.");
                                    return;
                                }

                                // ✅ BACKWARD SEEK ALLOWED
                                blockedSeek = false;
                                currentTime.textContent = formatTime(seekTo);
            });



            function applySeek() {
                isSeeking = false;

                // ❌ Do NOT apply blocked forward seek
                if (blockedSeek) return;

                audio.currentTime = Number(seekBar.value);
            }

            seekBar.addEventListener('mouseup', applySeek);
            seekBar.addEventListener('touchend', applySeek);

            /* =========================
            TIME UPDATE (MASTER SYNC)
            ========================= */
            audio.addEventListener('timeupdate', () => {
                const ct = audio.currentTime;
                const dur = audio.duration;
                if (!dur) return;

                const progress = ct / dur;

                progressCircle.style.strokeDashoffset =
                    CIRCUMFERENCE - (CIRCUMFERENCE * progress);

                if (!isSeeking) seekBar.value = ct;
                currentTime.textContent = formatTime(ct);

                lastSeekValue = Math.max(lastSeekValue, ct);
                localStorage.setItem(progressKey, ct);

                if (!audioFinished && ct >= dur - 0.1) {
                    markAudioFinished();
                }
            });

            /* =========================
            AUDIO FINISHED
            ========================= */
            function markAudioFinished() {
                audioFinished = true;
                localStorage.setItem(finishedKey, 'true');
                playIcon.classList.replace('fa-pause', 'fa-play');

                if (!quizCompleted) {
                    quizModal.classList.remove('hidden');
                }
            }

            audio.addEventListener('ended', markAudioFinished);

            /* =========================
            RESET ON REPLAY
            ========================= */
            audio.addEventListener('play', () => {
                if (audio.currentTime <= 0.1) {
                    audioFinished = false;
                    quizCompleted = false;
                    lastSeekValue = 0;

                    localStorage.setItem(finishedKey, 'false');
                    localStorage.setItem(quizKey, 'false');
                    localStorage.removeItem(progressKey);
                }
            });

            /* =========================
            QUIZ BUTTON
            ========================= */
            quizBtn.addEventListener('click', () => {
                if (!audioFinished) {
                    showWarning("Finish listening to the audio before taking the quiz.");
                    return;
                }
                if (quizCompleted) {
                    showWarning("Quiz already completed. Replay audio to retry.");
                    return;
                }
                quizModal.classList.remove('hidden');
            });

            /* =========================
            QUIZ SUBMIT (BACKEND SAFE)
            ========================= */
            document.getElementById('quizForm').addEventListener('submit', async e => {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) {
                        throw new Error('Server error');
                    }

                    const data = await res.json();

                    quizModal.classList.add('hidden');

                    /* ===== THIS IS WHERE IT GOES ===== */
                    if (data.status === 'success') {
                        quizCompleted = true;
                        localStorage.setItem(quizKey, 'true');

                        document.getElementById('rewardMessage').textContent = data.message;

                        rewardModal.classList.remove('hidden');
                    } else {
                        showWarning(
                            data.message || 'Some answers are incorrect. Listen again to retry.'
                        );
                    }
                    /* ===== END ===== */

                } catch (err) {
                    console.error(err);
                    showWarning('An unexpected error occurred. Please try again.');
                }
            });


            /* =========================
            CLOSE REWARD
            ========================= */
            closeReward.addEventListener('click', () => {
                rewardModal.classList.add('hidden');
            });

            /* =========================
            DOWNLOAD
            ========================= */
            downloadBtn.addEventListener('click', () => {
                const link = document.createElement('a');
                link.href = audio.src;
                link.download = '{{ $displayFile }}';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
    </script>



        
        <script src="https://kit.fontawesome.com/a2d9d5e6c1.js" crossorigin="anonymous"></script>
    </x-layouts.app>



