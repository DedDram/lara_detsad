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
    @if(!empty($district))
        @php
            $distr2 = trim(strrchr($district->name, ' '));
            $distr1 = trim(str_replace(array($distr2,'территориальный', 'административный'), '', $district->name));
        @endphp
        @if($district->morfer_name)
            <h1>Детские сады {{$district->morfer_name}}а г. {{$category->name}}</h1>
        @else
            <h1>Детские сады {{$distr2}}а {{$distr1}}</h1>
        @endif
        @if(count($items))
            <p>
                Количество детских садов, расположенных в {{$distr2}}е <strong>{{$distr1}}</strong> — {{count($items)}},
                для удобства мы покажем их на карте, и вы сможете выбрать ближайший к своему дому детский сад.
            </p>
        @else
            <p>
                К сожалению, пока неизвестно, какие садики есть в {{$distr2}}е <strong>{{$distr1}}</strong>.
            </p>
        @endif
    @else
        <div style="overflow: hidden;">{!! $category->text !!}</div>
    @endif
    <div>
        @if($districts->isNotEmpty())
            <b>По районам:</b>
            @foreach ($districts as $district)
                <a href="{{$baseUrl.'/'.$district->alias}}">{{$district->name}}</a>
            @endforeach
        @endif
    </div>
    <br>
    @if (!empty($category->city))
        <a href="/street/{{$category->city->id.'-'.$category->city->alias}}">Поиск по названию улицы</a>
    @endif
    <form action="https://detskysad.com/dobavit-sad" method="post" style="text-align: center;">
        <input type="submit" value="Добавить садик"/>
    </form>
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
