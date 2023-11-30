@extends('layouts')
@section('styles')
    @parent
    <link href="{{ asset('/css/cattable.css') }}" rel="stylesheet">
@endsection
@section('scripts')
    @parent
    <script src="{{ mix('/vue.js') }}" defer></script>
    <script src="{{ mix('js/yandex-map-vue.js') }}" defer></script>
    <script src="{{ mix('js/search-sadik-in-table.js') }}" defer></script>
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
                window.items = @json($items->unique('id')->values());
            </script>
            <yandex-map></yandex-map>
        </div>
        <div id="filter">
            <label>Поиск: (начните вводить название)
                <search-sadik-in-table></search-sadik-in-table>
            </label>
        </div>
    @else
        <br>
        <a href="https://detskysad.com/dobavit-sad">Добавить садик</a>
    @endif

@endsection

