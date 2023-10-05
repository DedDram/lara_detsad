@extends('layouts')
@section('scripts')
    @parent
    <script src="https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&load=package.full&lang=ru-RU"></script>
    <script src="{{ asset('/js/map.js') }}"></script>
@endsection
@section('content')
    <script type="text/javascript">
        var items = @json($address);
        ymaps.ready(function(){
            map.init('map');
            map.setClusterer(items);
            map.setCenter([items[0].geo_lat, items[0].geo_long], 8);
        });
    </script>
    <div id="map" style="width:100%; height: 400px; margin: 5px 0;"></div>

    <div>
        {!! $section->text !!}
    </div>
    <ul>
        @foreach ($categories as $category)
        <li>
            <a href="/{{$section->id.'-'.$section->alias.'/'.$category->id.'-'.$category->alias}}" class="category">{{$category->title}}</a>
        </li>
        @endforeach
    </ul>
@endsection

