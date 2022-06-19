@extends('layout.app')

@section('content')
    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Registration</p>
    <form class="mx-1 mx-md-4" method="POST" action="register.php">

        <div class="d-flex flex-row align-items-center mb-4">
            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
            <div class="form-outline flex-fill mb-0">
                <input type="text" name="email" id="email" class="form-control" value="{{ $email }}"/>
                <label class="form-label" for="email">Your email</label>
            </div>
        </div>
        <div class="d-flex flex-row align-items-center mb-4">
            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
            <div class="form-outline flex-fill mb-0">
                <input type="text" name="phone" id="phone" class="form-control" value="{{ $phone }}"/>
                <label class="form-label" for="phone">Your phone</label>
            </div>
        </div>
        <div class="d-flex flex-row align-items-center mb-4">
            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
            <div class="form-outline flex-fill mb-0">
                <input type="text" name="password" id="password" class="form-control" value="{{ $password }}"/>
                <label class="form-label" for="phone">Your password</label>
            </div>
        </div>
        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
            <button type="submit" name="register" class="btn btn-primary btn-lg">Register</button>
        </div>
        @if(!empty($message))
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
        @endif
    </form>
@endsection