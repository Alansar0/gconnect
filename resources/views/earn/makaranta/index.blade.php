<x-layouts.app>
    <div class="min-h-screen bg-[var(--bg-3)] text-[var(--text-1)] flex flex-col">
        <div class="max-w-3xl mx-auto">

            {{-- Fixed Header --}}
            <header class="fixed top-0 left-0 right-0 z-40 bg-[var(--unique-card-bg)] border-b border-[var(--card-border-friday)] shadow-[0_0_15px_var(--shadow-accent)]">
                <div class="text-center py-4 relative">
                    <a href="{{ url()->previous() }}" class="absolute left-6 top-1/2 -translate-y-1/2 text-[var(--accent)] hover:underline flex items-center">
                        <i class="material-icons mr-1 text-[var(--accent)]">arrow_back</i>
                        Back
                    </a>
                    <h1 class="text-[var(--text-1)] text-xl font-semibold tracking-wide">Makaranta</h1>
                </div>

                {{-- Makaranta Banner --}}
                <div class="flex justify-center mb-4 mt-3">
                    <div class="relative bg-[var(--bg-2)] rounded-2xl p-3 border border-[var(--accent)]/50 transition-all duration-300 shadow-[0_0_15px_var(--shadow-accent)] hover:shadow-[0_0_25px_var(--shadow-accent)] hover:scale-[1.02]">
                        <img src="{{ Vite::asset('resources/images/makaranta.png') }}" alt="Makaranta Image"
                             class="w-[85vw] max-w-[600px] h-44 md:h-56 object-contain mx-auto drop-shadow-[0_0_12px_var(--accent)]/40">
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-b from-transparent to-[var(--bg-3)]/60"></div>
                    </div>
                </div>

                {{-- Subheader --}}
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-[var(--accent)] mb-1">📚 Darasin Ilimi</h2>
                    <p class="text-[var(--text-1)]/80 text-sm px-4">
                        Faɗaɗa saninka game da Alƙur'ani da Sunnah. Bincika darussa da aka tsara don taimaka maka ka
                        fahimci kuma ka bunƙasa a cikin addininka cikin sauƙi.
                    </p>
                </div>
            </header>

            {{-- Main Section --}}
            <main class="flex-grow px-6 py-8 grid grid-cols-2 gap-5 overflow-y-auto mt-[390px]">
                @foreach ([ 
                    ['title' => 'kurakurai100', 'image' => 'kurakurai100.png', 'folder' => 'kurakurai100'],
                    ['title' => 'Sharrin Dajjal', 'image' => 'sharrindajjal.png', 'folder' => 'sharrindajjal'],
                    ['title' => 'Mu\'amalar Auratayya', 'image' => 'auretayya.png', 'folder' => 'auretayya'],
                    ['title' => 'KIMIYYA DA ALQUR\'ANI', 'image' => 'kimiyya.png', 'folder' => 'kimiyya'],
                    ['title' => 'FATAWOWI 30', 'image' => 'fatawowi30.png', 'folder' => 'fatawowi30'],
                    ['title' => 'Hukunci Jinin Mata', 'image' => 'hukunci.png', 'folder' => 'hukunci']
                ] as $item)
                    <div class="flex flex-col items-center">
                        <a href="{{ route('makaranta.darasi', ['course' => $item['folder']]) }}"
                           class="bg-[var(--unique-card-bg)] py-13 px-20 w-full rounded-2xl shadow-[0_0_12px_var(--shadow-accent)] flex items-center justify-center border border-[var(--accent)]/30 hover:bg-[var(--bg-2)] transition transform hover:scale-105 bg-center bg-cover hover:shadow-[0_0_20px_var(--shadow-accent)]"
                           style="background-image: url('{{ Vite::asset('resources/images/courses/' . $item['image']) }}');">
                        </a>
                        <span class="mt-2 text-sm font-semibold text-[var(--accent)] text-center">{{ $item['title'] }}</span>
                    </div>
                @endforeach
            </main>

        </div>
    </div>
</x-layouts.app>

