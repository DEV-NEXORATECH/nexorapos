<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="form-group mb-3">
        <label for="name">Name</label>
        <input id="name" name="name" type="text" class="form-control"
            value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name">
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control"
            value="{{ old('email', auth()->user()->email) }}" required autocomplete="username">
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
</form>
