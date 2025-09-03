<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="('New Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            <!-- Règles du mot de passe -->
            <div class="mt-2 text-sm">
                <ul class="list-disc pl-5 text-gray-600" id="password-rules">
                    <li class="password-rule text-red-600">❌ Minimum 10 caractères</li>
                    <li class="password-rule text-red-600">❌ Une lettre minuscule (a-z)</li>
                    <li class="password-rule text-red-600">❌ Une lettre majuscule (A-Z)</li>
                    <li class="password-rule text-red-600">❌ Un chiffre (0-9)</li>
                    <li class="password-rule text-red-600">❌ Un symbole (@$!%*#?&)</li>
                </ul>
            </div>

            <!-- Indicateur de force -->
            <div class="mt-2">
                <span id="password-strength" class="text-sm font-semibold">Force du mot de passe : <span class="text-red-600">Faible</span></span>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
     <script>
        const passwordInput = document.querySelector('#password');
        const rules = document.querySelectorAll('.password-rule');
        const strengthIndicator = document.querySelector('#password-strength span');

        passwordInput.addEventListener('input', () => {
            const value = passwordInput.value;

            const checks = [
                value.length >= 10,
                /[a-z]/.test(value),
                /[A-Z]/.test(value),
                /[0-9]/.test(value),
                /[@$!%*#?&]/.test(value)
            ];

            let passedCount = 0;

            checks.forEach((passed, index) => {
                const rule = rules[index];
                if (passed) {
                    rule.classList.remove('text-red-600');
                    rule.classList.add('text-green-600');
                    rule.innerHTML = rule.innerHTML.replace('❌', '✔️');
                    passedCount++;
                } else {
                    rule.classList.remove('text-green-600');
                    rule.classList.add('text-red-600');
                    rule.innerHTML = rule.innerHTML.replace('✔️', '❌');
                }
            });

            let strengthText = 'Faible';
            let strengthColor = 'text-red-600';

            if (passedCount >= 5) {
                strengthText = 'Fort';
                strengthColor = 'text-green-600';
            } else if (passedCount >= 3) {
                strengthText = 'Moyen';
                strengthColor = 'text-yellow-600';
            }
strengthIndicator.textContent = strengthText;
            strengthIndicator.className = strengthColor;
        });
    </script>
</x-guest-layout>
