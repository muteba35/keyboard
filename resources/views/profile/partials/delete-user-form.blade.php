<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Supprimer votre compte</h5>
        <small class="text-muted">Cette action est irréversible. Sauvegardez vos données avant de continuer.</small>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
                @if ($errors->userDeletion->has('password'))
                    <div class="text-danger small mt-1">{{ $errors->userDeletion->first('password') }}</div>
                @endif
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
            </div>
        </form>
    </div>
</div>