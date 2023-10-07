@extends('layouts')
@section('scripts')
    @parent
    <script src="https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&load=package.full&lang=ru-RU"></script>
    <script src="{{ asset('/js/map.js') }}"></script>
@endsection
@section('content')
555
@endsection

