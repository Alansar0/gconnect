<x-layouts.app>
<div class="flex flex-col items-center justify-center min-h-screen p-4">
    <h2 class="text-xl font-semibold mb-4">Enable Biometric</h2>

    <button id="registerBiometric"
        class="px-6 py-3 rounded bg-accent text-black font-semibold">
        Register Fingerprint
    </button>

    <p id="statusMessage" class="mt-4 text-red-600 hidden"></p>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
/* =========================
   Helpers
========================= */
function base64urlToBuffer(value) {
    if (!value || typeof value !== 'string') {
        throw new Error('Invalid base64url value from server');
    }

    const padding = '='.repeat((4 - value.length % 4) % 4);
    const base64 = (value + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const raw = atob(base64);
    return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
}

/* =========================
   Main Logic
========================= */
const button = document.getElementById('registerBiometric');
const status = document.getElementById('statusMessage');
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

button.addEventListener('click', async () => {
    status.classList.add('hidden');
    button.disabled = true;
    button.textContent = 'Processing...';

    try {
        /* 1️⃣ Request options from backend */
        const res = await fetch('/biometric/register/options', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });

        if (!res.ok) {
            throw new Error('Failed to fetch registration options');
        }

        const options = await res.json();
        console.log('RAW OPTIONS FROM SERVER:', options);

        /* 2️⃣ Normalize publicKey structure */
        const publicKey = options.publicKey ?? options;

        if (!publicKey.challenge) {
            throw new Error('Missing challenge in WebAuthn options');
        }

        if (!publicKey.user || !publicKey.user.id) {
            throw new Error('Missing user.id in WebAuthn options');
        }

        /* 3️⃣ Convert base64url → ArrayBuffer */
        publicKey.challenge = base64urlToBuffer(publicKey.challenge);
        publicKey.user.id = base64urlToBuffer(publicKey.user.id);

        if (Array.isArray(publicKey.excludeCredentials)) {
            publicKey.excludeCredentials = publicKey.excludeCredentials.map(cred => ({
                ...cred,
                id: base64urlToBuffer(cred.id)
            }));
        }

        console.log('FINAL PUBLIC KEY:', publicKey);

        /* 4️⃣ Create credential (THIS triggers fingerprint) */
        const credential = await navigator.credentials.create({
            publicKey
        });

        if (!credential) {
            throw new Error('Credential creation cancelled');
        }

        /* 5️⃣ Send credential to backend */
        const verifyRes = await fetch('/biometric/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(credential)
        });

        if (!verifyRes.ok) {
            throw new Error('Server verification failed');
        }

        /* 6️⃣ Done */
        window.location.href = '/profile';

    } catch (err) {
        console.error(err);
        status.textContent = err.message;
        status.classList.remove('hidden');
        button.disabled = false;
        button.textContent = 'Register Fingerprint';
    }
});
</script>


</x-layouts.app>


