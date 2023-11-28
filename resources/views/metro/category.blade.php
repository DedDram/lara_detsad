@extends('layouts')
@section('content')
    <h1>Детские сады возле метро г. {{$city}}</h1>
    <div style="overflow: hidden;">Здесь показан список станций метро г.{{$city}}, около которых расположены детские сады.
        Чтобы перейти на страничку с ближайшими детскими садами, кликните по названию метро.</div>
    <ul style="list-style-type: none;">
        @foreach($allMetroCity as $item)
            <li>
                <a href="/metro/{{$item->id}}-{{$item->alias}}">{{$item->name}}</a>
            </li>
        @endforeach
    </ul>
@endsection

