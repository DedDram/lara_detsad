@extends('layouts')
@section('meta')
    @parent
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('styles')
    @parent
    <link href="{{ asset('/css/sadik.css') }}" rel="stylesheet">
@endsection
@section('scripts')
    @parent
    <link rel="preconnect" href="//api-maps.yandex.ru">
    <link rel="dns-prefetch" href="//api-maps.yandex.ru">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=bbe8e134-9b68-440c-9769-df1a3dbf95a6&lang=ru-RU"
            type="text/javascript"></script>
    <script src="{{ mix('/js/simpleModal.js') }}" defer></script>
    <script>
        var item_id = {{$item->id}};
        var items = @json($address);
        document.addEventListener('DOMContentLoaded', function () {
            ymaps.ready(function () {
                var map = new ymaps.Map('mapGeoshow', {
                    center: [items[0].geo_lat, items[0].geo_long],
                    zoom: 13
                });

                // Функция для отправки запроса и обновления карты
                function updateMapData(geo) {
                    fetch('/ajax?task=map', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify(geo),
                    })
                        .then(response => {
                            return response.json();
                        })
                        .then(data => {
                            console.log(data);
                            map.geoObjects.removeAll();

                            data.forEach(item => {
                                var placemark = new ymaps.Placemark([item.geo_lat, item.geo_long], {
                                    iconContent: '<a href="' + item.url + '" target="_blank" style="text-decoration: none;">' + item.geo_code + '</a>',
                                }, {
                                    preset: "islands#blueStretchyIcon",
                                });
                                map.geoObjects.add(placemark);
                            });

                            var group = {};
                            data.forEach(function (row) {
                                if (!group[row.category_title]) {
                                    group[row.category_title] = {};
                                }
                                group[row.category_title][row.item_id] = row;
                            });

                            var resultsContainer = document.getElementById('results');
                            resultsContainer.innerHTML = '';

                            for (var title in group) {
                                if (group.hasOwnProperty(title)) {
                                    resultsContainer.innerHTML += '<h3>' + title + '</h3>';

                                    for (var itemId in group[title]) {
                                        if (group[title].hasOwnProperty(itemId)) {
                                            var row = group[title][itemId];
                                            resultsContainer.innerHTML += '<a href="' + row.url + '">' + row.geo_code + '</a><br>';
                                        }
                                    }
                                }
                            }
                        })
                }

                // Вызов функции при первой загрузке карты
                updateMapData({'geo_lat': items[0].geo_lat, 'geo_long': items[0].geo_long});

                // Событие изменения области карты (перетаскивание, масштабирование и т.д.)
                map.events.add('boundschange', function (e) {
                    var center = e.get('newCenter');
                    updateMapData({'geo_lat': center[0], 'geo_long': center[1]});
                });
            });
        });
    </script>
@endsection
@section('robots')
    @parent
    <meta name="robots" content="noindex, nofollow"/>
@endsection
@section('content')
    <!--beforecontent-->
    <ul id="tabs-menu">
        <li class="iphone5"><a href="{{$url}}">О садике</a></li>
        <li class="tabmobile"><a rel="nofollow" href="{{$url}}/gallery">Фото <span
                    class="redtext">{{!empty($item->count_img) ? $item->count_img : 0}}</span></a></li>
        <li class="tabmobile"><a rel="nofollow" href="{{$url}}/agent">Руководство <span
                    class="redtext">{{!empty($item->user_id_agent) ? 1 : ''}}</span></a></li>
        <li class="active"><a rel="nofollow" href="{{$url}}/geoshow">Сады рядом <span
                    class="redtext">{{$item->nearby ?? 0}}</span></a></li>
        @if($item->ads_url)
            <li><a rel="nofollow" href="{{$item->ads_url}}">Обмен</a></li>
        @endif
    </ul>

    <div style="margin-bottom: 10px;">
        Вы можете перетаскивать карту в нужном направлении, детские сады и центры расположенные рядом, будут
        подгружаться на карту автоматически.
        <div style="margin-bottom: 10px;">
            <div style="overflow: hidden;">
                <div id="mapGeoshow" style="width:100%; height: 400px; margin: 20px 0;"></div>
                <div id="results"></div>
            </div>
        </div>
        <div style="padding-top: 9px; text-align: right;">
            <div style="padding-bottom: 5px; text-align: left;padding-top: 5px;">
                <a href="/post/error?id={{$item->id}}"
                   class="simplemodal find_error_btn" data-width="450" data-height="430"
                   style="vertical-align: middle;">Нашли ошибку?</a>
            </div>
        </div>
    </div>
@endsection

