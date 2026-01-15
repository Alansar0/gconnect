
<x-layouts.app>
    <div class="min-h-screen bg-bg2 text-t1 flex flex-col items-center justify-start font-['Roboto']">

        <!-- Back Button -->
        <div class="w-full flex justify-start mt-6 mb-4 px-6">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Header Section --> 
        <div class="relative w-full h-64 bg-cover bg-[center_top_9rem] rounded-b-3xl shadow-lg bg-[url('../images/mosque-bg-night.png')]">
            <div class="absolute bottom-0 left-0 right-0 flex justify-center">
                <div class="bg-bg3 border rounded-2xl px-5 py-4 w-11/12 max-w-md shadow-xl"
                     style="border-color: var(--unique-bottom-border);">

                    <div class="bg-gradient-to-br from-bg2 to-bg3 rounded-2xl p-5 shadow-md text-t1">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-accent">Rewards</h2>
                            <button class="text-accent hover:text-t1 transition">
                                <i class="fas fa-ellipsis-h text-lg"></i>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="flex justify-between items-center">
                            <!-- Cashback -->
                            <div class="flex flex-col items-start">
                                <p class="text-t2 text-sm flex items-center gap-1">
                                    Cashback
                                    <i class="fas fa-question-circle text-xs text-t3"></i>
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="bg-yellow-500/20 p-1.5 rounded-full">
                                        <i class="fas fa-coins text-yellow-400"></i>
                                    </div>
                                    <p class="font-semibold text-base text-t1">₦ {{ number_format($cashback, 2) }}</p>
                                    <i class="fas fa-chevron-right text-t2 text-sm"></i>
                                </div>
                            </div>

                            <!-- Voucher -->
                            <div class="flex flex-col items-end">
                                <p class="text-t2 text-sm">Voucher</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="bg-accent/20 p-1.5 rounded-md text-accent dark:text-[#00FFD1]">
                                        <x-voucher> </x-voucher>
                                    </div>
                                    <p class="font-semibold text-base text-t1">{{ $vouchers }}</p>
                                    <i class="fas fa-chevron-right text-t2 text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center absolute left-0 right-0 z-10">
                        <button onclick="window.location.href='{{ route('earn.makaranta.index') }}'"
                                class="bg-accent text-bg1 px-8 py-2 rounded-xl shadow-md hover:bg-[#1f6feb] transition">
                            Earn More Vouchers →
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spacer -->
        <div class="h-16"></div>

        <!-- Options Section -->
        <div class="grid grid-cols-2 gap-4 px-6 mt-2 w-full max-w-md">
            @foreach([
                ['route'=> 'earn.morningAzkar', 'icon'=>'fa-regular fa-sun', 'label'=>'Morning Dhikr'],
                ['route'=> 'earn.eveningAzkar', 'icon'=>'fa-regular fa-moon', 'label'=>'Evening Dhikr'],
                ['route'=> 'earn.makaranta.index', 'icon'=>'fas fa-book-open', 'label'=>'Makaranta', 'coming_soon' => true],
                ['route'=> 'earn.friday', 'icon'=>'fas fa-mosque', 'label'=>'Friday Gifts'],
            ] as $option)
                <a href="{{ route($option['route']) }}"
                   class="bg-bg3 border rounded-xl p-5 text-center hover:shadow-md transition"
                   style="border-color: var(--unique-bottom-border); 
                          box-shadow: 0 0 4px var(--unique-bottom-shadow);">
                    <i class="{{ $option['icon'] }} text-accent text-2xl mb-2"></i>
                    <p class="font-medium text-t1">{{ $option['label'] }}</p>
                </a>
            @endforeach
        </div>

      
        
         <!-- Hadith Section -->
        <div class="bg-bg3 border shadow-md rounded-2xl p-4 mt-6 mx-4 max-w-md"
            style="border-color: var(--unique-bottom-border);">
            <h3 class="text-accent font-semibold mb-2">Hadith of the Day</h3>
            <p class="text-t2 text-sm leading-relaxed">
                The Prophet Muhammad ﷺ said:
                <span class="italic text-accent">
                    “Whoever travels a path in search of knowledge, Allah will make easy for him a path to Paradise.”
                </span>
                <br>
                <span class="text-xs text-t3">— Sahih Muslim</span>
            </p>
            <div class="flex justify-center mt-3">
                <button class="text-accent text-sm font-medium hover:underline">
                    Reflect 📘
                </button>
            </div>
        </div>

    </div>



</x-layouts.app>


