@extends('layouts')
@section('scripts')
    @parent
    <script src="{{ mix('js/yandex-map.js') }}" defer></script>
    <link rel="preconnect" href="//api-maps.yandex.ru">
    <link rel="dns-prefetch" href="//api-maps.yandex.ru">
@endsection
@section('content')
    <div id="app">
        <script>
            window.address = @json($address);
        </script>
        <div id="yandex-map" style="width: 100%; height: 400px;"></div>
        <div id="cluster-count"></div>
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


