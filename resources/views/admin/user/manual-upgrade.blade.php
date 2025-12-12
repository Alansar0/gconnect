<x-layouts.app>
<div class="max-w-md mx-auto bg-[#0C141C] text-white rounded-xl p-6 shadow-lg mt-10">
    <div class=" w-full flex justify-start -mt-4 mb-3">
            <a onclick="window.history.back()" class="text-[#58a6ff] hover:underline flex items-center">
    <i class="material-icons mr-1">arrow_back</i> Back
</a>
        </div>
    <h2 class="text-xl font-bold mb-4 text-center">Manual Reseller Upgrade</h2>

    <!-- Success Popup -->
    <div id="popup-success" class="hidden bg-green-600 text-white p-3 rounded-lg mb-3 text-center"></div>

    <!-- Error Popup -->
    <div id="popup-error" class="hidden bg-red-600 text-white p-3 rounded-lg mb-3 text-center"></div>

    <form id="upgradeForm">
        @csrf
        {{-- <label>User phone/email</label> --}}
        <label>User ID</label>
        <input type="text" name="user_id" class="w-full p-2 mb-3 rounded bg-gray-800" required>

        <label>Hotspot Name</label>
        <input type="text" name="name" class="w-full p-2 mb-3 rounded bg-gray-800" required>

        <label>Host</label>
        <input type="text" name="host" class="w-full p-2 mb-3 rounded bg-gray-800" required>

        <label>Port</label>
        <input type="number" name="port" value="8728" class="w-full p-2 mb-3 rounded bg-gray-800" required>

        <label>Username</label>
        <input type="text" name="username" class="w-full p-2 mb-3 rounded bg-gray-800" required>

        <label>Password</label>
        <input type="password" name="password" class="w-full p-2 mb-3 rounded bg-gray-800" required>

        <button type="submit" class="bg-[#00FFD1] text-black px-4 py-2 rounded-lg w-full font-semibold">
            Upgrade User
        </button>
    </form>
</div>

<script>
document.getElementById('upgradeForm').addEventListener('submit', async function (e) {
    e.preventDefault(); // prevent page reload

    const form = e.target;
    const formData = new FormData(form);
    const popupSuccess = document.getElementById('popup-success');
    const popupError = document.getElementById('popup-error');

    // reset popups
    popupSuccess.classList.add('hidden');
    popupError.classList.add('hidden');

    try {
        const response = await fetch("{{ route('admin.reseller.upgrade') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": formData.get('_token'),
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            popupSuccess.textContent = data.message;
            popupSuccess.classList.remove('hidden');
            form.reset();
        } else {
            popupError.textContent = data.message || 'Something went wrong.';
            popupError.classList.remove('hidden');
        }

    } catch (error) {
        popupError.textContent = 'Server error. Please try again.';
        popupError.classList.remove('hidden');
    }

    // Hide messages after 4 seconds
    setTimeout(() => {
        popupSuccess.classList.add('hidden');
        popupError.classList.add('hidden');
    }, 15000);
});
</script>
</x-layouts.app>
