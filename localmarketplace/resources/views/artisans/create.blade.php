@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Créer un nouvel artisan</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('artisans.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Adresse</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" required>
        </div>

        <div class="mb-3">
            <label for="rib" class="form-label">RIB</label>
            <input type="text" name="rib" id="rib" class="form-control" value="{{ old('rib') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Créer</button>
        <a href="{{ route('artisans.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
