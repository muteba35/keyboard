
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php
        $locked = session('locked', 0);
        $blockedUser = session('blocked_user', false);
    @endphp

    <!-- Message si le compte est bloqué -->
    @if ($blockedUser)
        <div class="mt-4 text-sm text-red-600 text-center font-semibold" id="block-message">
            Pour des raisons de sécurité, votre compte a été temporairement bloqué après plusieurs tentatives de connexion échouées. Veuillez réessayez après 1 heure.
        </div>
    @endif

    <!-- Message si délai d'attente -->
    @if ($locked && !$blockedUser)
        <div class="mt-4 text-sm text-red-600 text-center">
            Trop de tentatives. Réessayez dans <span id="countdown">{{ $locked }}</span> secondes.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email')" required autofocus autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Register Link -->
        <div class="block mt-4">
            <label class="inline-flex items-center">
                {{ ("Don't have an account?") }}
                <span class="ms-2 text-sm text-gray-600">
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-semibold">Register</a>
                </span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1" href="{{ route('password.request') }}">
                    <i class="fas fa-lock text-gray-500"></i>
                    {{ ('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- JavaScript -->
    <script>
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginButton = document.querySelector('button[type="submit"]');

        // Si utilisateur bloqué
        @if ($blockedUser)
            emailInput.setAttribute('readonly', true);
            passwordInput.setAttribute('readonly', true);
            loginButton.disabled = true;
            loginButton.classList.add('opacity-50', 'cursor-not-allowed');
        @endif

        // Compte à rebours si temporairement bloqué
        @if ($locked && !$blockedUser)
            let seconds = {{ json_encode($locked) }};
            const countdown = document.getElementById('countdown');
            const messageContainer = document.querySelector('.mt-4.text-red-600');

            emailInput.setAttribute('readonly', true);
            passwordInput.setAttribute('readonly', true);
            loginButton.disabled = true;
            loginButton.classList.add('opacity-50', 'cursor-not-allowed');

            const interval = setInterval(() => {
                if (seconds > 1) {
                    seconds--;
                    countdown.textContent = seconds;
                } else {
                    clearInterval(interval);

                    messageContainer.textContent = 'Maintenant, vous pouvez écrire.';
                    messageContainer.classList.remove('text-red-600');
                    messageContainer.classList.add('text-green-600');

                    emailInput.removeAttribute('readonly');
                    passwordInput.removeAttribute('readonly');
                    loginButton.disabled = false;
                    loginButton.classList.remove('opacity-50', 'cursor-not-allowed');

                    setTimeout(() => {
                        messageContainer.style.display = 'none';
                    }, 5000);
                }
            }, 1000);
        @endif

        // Masquer "too many login attempts" (Laravel)
        const errorMessages = document.querySelectorAll('.text-sm.text-red-600');
        errorMessages.forEach((el) => {
            if (el.textContent.includes('too many login attempts')) {
                setTimeout(() => {
                    el.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</x-guest-layout>