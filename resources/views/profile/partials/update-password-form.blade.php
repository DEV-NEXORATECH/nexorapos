<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="form-group mb-3">
        <label for="current_password">Current Password</label>
        <input id="current_password" name="current_password" type="password" class="form-control" required autocomplete="current-password">
        @error('current_password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password">New Password</label>
        <input id="password" name="password" type="password" class="form-control" required autocomplete="new-password">
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required autocomplete="new-password">
        @error('password_confirmation')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
</form>
