@extends('layouts')
@section('robots')
    @parent
    <meta name="robots" content="noindex, nofollow" />
@endsection
@section('content')
    Регистрация подтверждена! {{ session('agent') ?? '' }}
@endsection

