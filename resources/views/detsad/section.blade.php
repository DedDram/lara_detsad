@extends('layouts')
@section('scripts')
    @parent
    <script src="https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&load=package.full&lang=ru-RU"></script>
    <script src="{{ mix('js/app.js') }}"></script>
@endsection
@section('content')
    <script type="text/javascript">
            var items = @json($address);
            ymaps.ready(function () {
            new Vue({
                el: '#yandex-map',
                mounted() {
                    this.$refs.map.initMap();
                    this.$refs.map.setClusterer(items);
                    this.$refs.map.setCenter([items[0].geo_lat, items[0].geo_long], 8);
                },
            });
        });
    </script>
    <div id="yandex-map">
        <yandex-map ref="map"></yandex-map>
    </div>
    <div>
        {!! $section->text !!}
    </div>
    <ul>
        @foreach ($categories as $category)
            <li>
                <a href="/{{$section->id.'-'.$section->alias.'/'.$category->id.'-'.$category->alias}}"
                   class="category">{{$category->title}}</a>
            </li>
        @endforeach
    </ul>
@endsection

