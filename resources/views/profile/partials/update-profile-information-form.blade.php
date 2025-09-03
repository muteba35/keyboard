<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Informations du profil</h5>
        <small class="text-muted">Mettez à jour vos informations personnelles et votre adresse e-mail.</small>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning">
                    Votre adresse e-mail n’est pas encore vérifiée.
                    <button type="submit" form="send-verification" class="btn btn-link p-0 m-0 align-baseline">Renvoyer le mail de vérification</button>
                </div>
                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2">
                        Un nouveau lien de vérification a été envoyé.
                    </div>
                @endif
            @endif

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>