@extends('layouts')

@section('content')
    <h1>Детские сады — {{$streets['city']}}</h1>
    <div style="overflow: hidden;">Здесь показан список улиц, на которых расположены детские сады. Чтобы перейти на
        страничку с детскими садами, кликните по названию улицы.
    </div>
    <ul style="list-style-type: none;">
    @php
    $city_id = $streets['city_id'];
    $city_alias = $streets['city_alias'];
    @endphp
    @foreach ($streets['streets'] as $street)
        @if(count($street)>1)
            <li>
                <a href="/street/{{$city_id.'-'.$city_alias.'/'.$street[0]->alias}}">Детские сады — {{$street[0]->name}}</a>
            </li>
        @endif
    @endforeach
</ul>
@endsection

