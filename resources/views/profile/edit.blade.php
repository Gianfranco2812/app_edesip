@extends('layouts.admin')

@section('content')
    <div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Mi Perfil</h3>
    </div>
    
    <div class="row">
        
        <div class="col-md-4">
        <div class="card card-profile">
            <div class="card-header" style="background-image: url('{{ asset('assets/img/blogpost.jpg') }}')">
            <div class="profile-picture">
                <div class="avatar avatar-xl">
                @if($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="..." class="avatar-img rounded-circle">
                @else
                    {{-- Foto por defecto si no tiene --}}
                    <img src="{{ asset('assets/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle">
                @endif
                </div>
            </div>
            </div>
            <div class="card-body">
            <div class="user-profile text-center">
                <div class="name">{{ $user->name }}</div>
                <div class="job">{{ $user->roles->pluck('name')->first() ?? 'Usuario' }}</div>
                <div class="desc">{{ $user->email }}</div>
                
                <div class="view-profile mt-3">
                    <span class="badge badge-secondary">Cuenta Activa</span>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="col-md-8">
        <div class="card">
            <div class="card-header">
            <h4 class="card-title">Configuración de Cuenta</h4>
            </div>
            <div class="card-body">
            
            <ul class="nav nav-pills nav-secondary" id="pills-tab" role="tablist">
                <li class="nav-item">
                <a class="nav-link active" id="pills-general-tab" data-bs-toggle="pill" href="#pills-general" role="tab">Información General</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" id="pills-security-tab" data-bs-toggle="pill" href="#pills-security" role="tab">Cambiar Contraseña</a>
                </li>
            </ul>

            <div class="tab-content mt-4 mb-3" id="pills-tabContent">
                
                <div class="tab-pane fade show active" id="pills-general" role="tabpanel">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre Completo</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Correo Electrónico</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Foto de Perfil</label>
                                    <input type="file" class="form-control" name="photo" accept="image/*">
                                    <small class="form-text text-muted">Sube una imagen cuadrada (JPG, PNG) de máx 2MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="pills-security" role="tabpanel">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Contraseña Actual</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
                                    @error('current_password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nueva Contraseña</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-warning">Actualizar Contraseña</button>
                        </div>
                    </form>
                </div>

            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
@endsection