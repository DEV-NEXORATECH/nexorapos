<form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account?');">
    @csrf
    @method('delete')

    <div class="form-group mb-3">
        <label for="password">Password (confirm to delete account)</label>
        <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-danger">Delete Account</button>
</form>
