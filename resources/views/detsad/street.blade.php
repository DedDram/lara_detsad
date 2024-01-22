@extends('layouts')
@section('styles')
    @parent
    <link href="{{ asset('/css/cattable.css') }}" rel="stylesheet">
@endsection
@section('scripts')
    @parent
    <script src="{{ mix('js/yandex-map.js') }}" defer></script>
    <link rel="preconnect" href="//api-maps.yandex.ru">
    <link rel="dns-prefetch" href="//api-maps.yandex.ru">
@endsection
@section('content')
    <h1>Детские сады — {{$street->name}} ({{$street->city}})</h1>
    <p>
        На этой странице Вы можете посмотреть детские сады, расположенные по адресу <strong>{{$street->name}}</strong> в городе {{$street->city}}.
        Кликнув по ссылке детского сада на карте, можно перейти на его страничку.
        Прочитав отзывы о детских садах, Вы сможете выбрать хороший детский сад своему ребенку и определиться, какой лучше ему подойдет.
    </p>

    @if(!empty($items))
        <div id="app">
            <script>
                window.address = @json($address);
            </script>
            <div id="yandex-map" style="width: 100%; height: 400px;"></div>
            <div id="cluster-count"></div>
        </div>

    @else
        <br>
        <a href="https://detskysad.com/dobavit-sad">Добавить садик</a>
    @endif

@endsection

