<extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ajouter un produit</h1>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nom du produit</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Prix (€)</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="qty_available" class="form-label">Quantité disponible</label>
            <input type="number" step="0.01" name="qty_available" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>
@endsection