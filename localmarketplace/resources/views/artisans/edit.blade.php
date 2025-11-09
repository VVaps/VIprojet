<x-app-layout>
    <div class="container">
        <h1>Modifier l'artisan : {{ $artisan->name }}</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('artisans.update', $artisan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $artisan->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $artisan->address) }}" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $artisan->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Téléphone </label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $artisan->phone) }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="rib" class="form-label">RIB</label>
                <input type="text" name="rib" id="rib" class="form-control" value="{{ old('rib', $artisan->rib) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $artisan->description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('artisans.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</x-app-layout>
