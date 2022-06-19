@extends('layout.app')

@section('content')
    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Phone validation</p>
    <form class="mx-1 mx-md-4" method="POST" action="/">

        <div class="d-flex flex-row align-items-center mb-4">
            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
            <div class="form-outline flex-fill mb-0">
                <input type="text" name="phone" id="phone" class="form-control" value="{{ $phone }}"/>
                <label class="form-label" for="phone">Your Phone</label>
            </div>
        </div>
        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
            <div class="mb-lg-6 mr-3">
                <a href="password.php" class="btn btn-primary btn-lg">Password?</a>
            </div>
            <div class="mb-lg-6">
                <button type="submit" name="verification" class="btn btn-primary btn-lg">Send</button>
            </div>
        </div>
        @if($message)
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
        @endif
    </form>
@endsection