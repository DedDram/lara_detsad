@extends('layouts')
@section('styles')
    @parent
    <link href="{{ asset('/css/cattable.css') }}" rel="stylesheet">
@endsection
@section('scripts')
    @parent
    <script src="{{ asset('/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('/js/cattable.js') }}"></script>
    <script src="{{ asset('/js/jquery.dataTables.1.10.7.min.js') }}"></script>
    <script
        src="https://api-maps.yandex.ru/2.1/?apikey=067bbf35-de27-4de2-bb1c-72d958556cad&load=package.full&lang=ru-RU"></script>
    <script src="{{ asset('/js/map.js') }}"></script>
    <script type="text/javascript">
        var items = @json($address);
        ymaps.ready(function () {
            map.init('map');
            map.setClusterer(items);
            map.setCenter([items[0].geo_lat, items[0].geo_long], {{ !empty($district) ? 11 : 10 }});
        });
    </script>
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
        <div id="map" style="width:100%; height: 300px; margin: 5px 0;"></div>

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
                            <th class="sectiontableheader" style="text-align:left;">
                            </th>
                        </tr>
                        </thead>
                        @foreach ($items as $item)
                            <tr class="sectiontableentry">
                                <td data-label="№">{{$item->n}}</td>
                                <td><a href="{{$item->link}}">{{$item->name}}</a></td>
                                <td data-label="Рейтинг">{{round($item->average, 1)}}</td>
                                <td data-label="Отзывов о садике">{{$item->comments}}</td>
                                <td>
                                    <h3>{{$item->okrug}}</h3>
                                </td>
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
