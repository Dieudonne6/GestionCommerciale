@extends('layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Mon Profil</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="profile-photo-container">
                                    @if ($utilisateur->photo)
                                        <img src="data:image/jpeg;base64,{{ base64_encode($utilisateur->photo) }}"
                                            alt="Photo de profil" class="img-thumbnail profile-photo">
                                    @else
                                        <div class="no-photo">
                                            <i class="fas fa-user fa-5x"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $utilisateur->nom }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="mail" class="form-label">Mail</label>
                                        <input type="email" class="form-control" id="mail" name="mail"
                                            value="{{ $utilisateur->mail }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="photo" class="form-label">Photo de profil</label>
                                        <input type="file" class="form-control" id="photo" name="photo"
                                            accept="image/*">
                                        <small class="text-muted">Formats acceptés : JPEG, PNG, JPG, GIF. Taille maximale :
                                            2MB</small>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-1"></i> Mettre à jour
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-photo-container {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #dee2e6;
        }

        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-photo {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            color: #6c757d;
        }
    </style>
@endsection
