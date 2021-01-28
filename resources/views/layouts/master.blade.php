@extends('layouts.app')


@section('content')
<div class="container background">
    <div class="row">
        <div class="col-md-8">
            @yield('content-master')
        </div>
        <div class="col-md-4">
            @include('partials.sidebar')
        </div>
    </div>
</div>
@endsection