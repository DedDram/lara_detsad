@extends('layouts')
@section('styles')
    @parent
    <link href="{{ asset('/css/cattable.css') }}" rel="stylesheet">
@endsection
@section('scripts')
    @parent
    <script src="{{ mix('js/yandex-map.js') }}" defer></script>
    <script src="{{ mix('js/search-sadik-in-table.js') }}" defer></script>
    <link rel="preconnect" href="//api-maps.yandex.ru">
    <link rel="dns-prefetch" href="//api-maps.yandex.ru">
@endsection
@section('content')
    <h1>Детские сады {{$address[0]->metroName.' г.'.$address[0]->city}}</h1>
    <script>
        window.address = @json($address);
        window.items = @json($address);
    </script>

    @if(!empty($address))
        <div id="yandex-map" style="width: 100%; height: 400px;"></div>
        <div id="cluster-count"></div>
        <input type="text" id="searchQuery" placeholder="Поиск по названию или номеру...">
        <table style="width:100%;border:0;text-align:center" class="luchie">
            <tbody>
            <tr>
                <td class="contentdescription" colspan="2"></td>
            </tr>
            <tr>
                <td>
                    <table class="display" id="cattable">
                        <thead>
                        <tr>
                            <th class="sectiontableheader" style="text-align:right;width:5%">Num</th>
                            <th class="sectiontableheader" style="text-align:left;">Название</th>
                            <th class="sectiontableheader" style="text-align:left;">Рейтинг</th>
                            <th class="sectiontableheader" style="text-align:left;">Отзывов о садике</th>
                        </tr>
                        </thead>
                        @foreach ($address as $item)
                            <tr class="sectiontableentry">
                                <td data-label="№">{{$item->n}}</td>
                                <td><a href="{{$item->link}}">{{$item->name}}</a></td>
                                <td data-label="Рейтинг">{{round($item->average, 1)}}</td>
                                <td data-label="Отзывов о садике">{{$item->comments}}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    @else
        <br>
        <a href="https://detskysad.com/dobavit-sad">Добавить садик</a>
    @endif

@endsection
