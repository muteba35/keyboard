<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Modifier le mot de passe</h5>
        <small class="text-muted">Utilisez un mot de passe long et complexe pour renforcer la sécurité.</small>
    </div>
    <div class="card-body">

        @if (session('status') === 'password-updated')
            <div class="alert alert-success">✔️ Mot de passe modifié avec succès.</div>
        @elseif (session('status') === 'password-update-failed')
            <div class="alert alert-danger">❌ Le mot de passe actuel est incorrect.</div>
        @endif

        @if ($errors->updatePassword->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->updatePassword->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="current_password" class="form-label">Mot de passe actuel</label>
                <input type="password" name="current_password" class="form-control" autocomplete="current-password">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" id="update_password_password" class="form-control" autocomplete="new-password">
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">Modifier</button>
            </div>
        </form>

        <div class="mt-3">
            <small class="text-muted">Règles de sécurité :</small>
            <ul class="small text-muted list-unstyled" id="password-rules">
                <li class="password-rule text-danger">❌ Minimum 10 caractères</li>
                <li class="password-rule text-danger">❌ Une lettre minuscule (a-z)</li>
                <li class="password-rule text-danger">❌ Une lettre majuscule (A-Z)</li>
                <li class="password-rule text-danger">❌ Un chiffre (0-9)</li>
                <li class="password-rule text-danger">❌ Un symbole (@$!%*#?&)</li>
            </ul>

            <p class="mt-2"><strong>Force du mot de passe : <span id="password-strength" class="text-danger">Faible</span></strong></p>
        </div>

        <script>
            const passwordInput = document.querySelector('#update_password_password');
            const rules = document.querySelectorAll('.password-rule');
            const strengthIndicator = document.querySelector('#password-strength');

            passwordInput.addEventListener('input', () => {
                const value = passwordInput.value;
                const checks = [
                    value.length >= 10,
                    /[a-z]/.test(value),
                    /[A-Z]/.test(value),
                    /[0-9]/.test(value),
                    /[@$!%*#?&]/.test(value)
                ];

                let passed = 0;
                checks.forEach((ok, i) => {
                    const r = rules[i];
                    r.classList.toggle('text-success', ok);
                    r.classList.toggle('text-danger', !ok);
                    r.innerHTML = r.innerHTML.replace(/✔️|❌/, ok ? '✔️' : '❌');
                    if (ok) passed++;
                });

                strengthIndicator.textContent = passed >= 5 ? 'Fort' : passed >= 3 ? 'Moyen' : 'Faible';
                strengthIndicator.className = passed >= 5 ? 'text-success' : passed >= 3 ? 'text-warning' : 'text-danger';
            });
        </script>
    </div>
</div>