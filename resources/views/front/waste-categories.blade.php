@extends('front.layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center text-success">Waste Categories</h1>


    
    <div class="mb-4 text-right">
        <a href="{{ route('front.waste-categories.create') }}" class="btn btn-success">
           +
        </a>
    </div>

    @if($categories->isEmpty())
        <p class="text-muted">No categories available yet.</p>
    @else
        <div class="row">
            @foreach($categories as $category)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-success text-center">Name:{{ $category->name }}</h5>
                            <p class="card-text">Description:{{ $category->description }}</p>
                            <small class="text-muted mb-3">Recycling_Instructions: {{ $category->recycling_instructions }}</small>
                            
                            
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="{{ route('front.waste-categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('front.waste-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
