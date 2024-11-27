@extends('layouts.master')

@section('content')
    <div class="container mt-5">

        <div class="card shadow-sm">
            @if (session('success'))
            <div class="alert alert-success" id="successMessage">
                {{ session('success') }}
            </div>
            @endif
            <div class="card-body">
                <h4 class="mb-4">Informations de l'entreprise</h4>
                <form action="{{ route('entreprise.storeEntreprise') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Row 1 -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nomEntreprise" class="form-label">Nom de l'entreprise</label>
                            <input type="text" id="nomEntreprise" name="nomEntreprise" class="form-control"
                                value="{{ $entreprise->nomEntreprise ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="adresseEntreprise" class="form-label">Adresse</label>
                            <input type="text" id="adresseEntreprise" name="adresseEntreprise" class="form-control"
                                value="{{ $entreprise->adresseEntreprise ?? '' }}">
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="emailEntreprise" class="form-label">Email</label>
                            <input type="email" id="emailEntreprise" name="emailEntreprise" class="form-control"
                                value="{{ $entreprise->emailEntreprise ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" class="form-control"
                                value="{{ $entreprise->telephone ?? '' }}">
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="IFU" class="form-label">IFU</label>
                            <input type="text" id="IFU" name="IFU" class="form-control"
                                value="{{ $entreprise->IFU ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="site_web" class="form-label">Site Web</label>
                            <input type="url" id="site_web" name="site_web" class="form-control"
                                value="{{ $entreprise->site_web ?? '' }}">
                        </div>
                    </div>

                    <!-- Row 5 -->
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" id="logo" name="logo" class="form-control">
                        @if ($entreprise && $entreprise->logo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $entreprise->logo) }}" alt="Logo de l'entreprise"
                                    class="img-thumbnail" width="150">
                            </div>
                        @endif
                    </div>

                    <!-- Row 4 -->
                    <div class="mb-3">
                        <label for="Description" class="form-label">Description</label>
                        <textarea id="Description" name="Description" class="form-control" rows="3">{{ $entreprise->Description ?? '' }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            let successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 3000); // Le message disparaît après 3 secondes (3000 ms)
    </script>
@endsection
