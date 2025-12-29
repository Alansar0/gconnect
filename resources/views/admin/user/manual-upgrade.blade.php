

<x-layouts.app>
    <div class="max-w-md mx-auto bg-bg2 dark:bg-bg2 text-t1 dark:text-t1 rounded-xl p-6 shadow-lg mt-10">

        <!-- Back Button -->
        <div class="w-full flex justify-start -mt-4 mb-3">
            <a onclick="window.history.back()" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <h2 class="text-xl font-bold mb-4 text-center text-accent dark:text-accent">Manual Reseller Upgrade</h2>

        <!-- Success Popup -->
        <div id="popup-success" class="hidden bg-green-600 text-bg1 p-3 rounded-lg mb-3 text-center"></div>

        <!-- Error Popup -->
        <div id="popup-error" class="hidden bg-red-500 text-bg1 p-3 rounded-lg mb-3 text-center"></div>

    <form id="upgradeForm" onsubmit="return false;">
            @csrf

            <label class="block text-t2 dark:text-t2 mb-1">User ID</label>
            <input type="text" name="user_id" class="w-full p-2 mb-3 rounded bg-bg3 dark:bg-bg3 text-t1 dark:text-t1" required>

            <label class="block text-t2 dark:text-t2 mb-1">Hotspot Name</label>
            <input type="text" name="name" class="w-full p-2 mb-3 rounded bg-bg3 dark:bg-bg3 text-t1 dark:text-t1" required>

            <label class="block text-t2 dark:text-t2 mb-1">Host</label>
            <input type="text" name="host" class="w-full p-2 mb-3 rounded bg-bg3 dark:bg-bg3 text-t1 dark:text-t1" required>

            <label class="block text-t2 dark:text-t2 mb-1">Port</label>
            <input type="number" name="port" value="8728" class="w-full p-2 mb-3 rounded bg-bg3 dark:bg-bg3 text-t1 dark:text-t1" required>

            <label class="block text-t2 dark:text-t2 mb-1">Username</label>
            <input type="text" name="username" class="w-full p-2 mb-3 rounded bg-bg3 dark:bg-bg3 text-t1 dark:text-t1" required>

            <label class="block text-t2 dark:text-t2 mb-1">Password</label>
            <input type="password" name="password" class="w-full p-2 mb-3 rounded bg-bg3 dark:bg-bg3 text-t1 dark:text-t1" required>

            <button id="submitBtn" type="submit" class="bg-accent text-bg1 px-4 py-2 rounded-lg w-full font-semibold hover:opacity-90">
                Upgrade User
            </button>

        </form>
    </div>

    <script>
        const form = document.getElementById('upgradeForm');
        const submitBtn = document.getElementById('submitBtn');
        const popupSuccess = document.getElementById('popup-success');
        const popupError = document.getElementById('popup-error');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // prevent double click
            if (submitBtn.disabled) return;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            popupSuccess.classList.add('hidden');
            popupError.classList.add('hidden');

            try {
                const formData = new FormData(form);

                const response = await fetch("{{ route('admin.reseller.upgrade') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                popupSuccess.textContent = data.message;
                popupSuccess.classList.remove('hidden');
                form.reset();

            } catch (error) {
                popupError.textContent =
                    error?.message ||
                    Object.values(error?.errors || {}).flat().join(' ') ||
                    'Request failed. Please try again.';
                popupError.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Upgrade User';
            }
        });
    </script>
</x-layouts.app>

