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
    <script src="{{ mix('/js/simpleModal.js') }}" defer></script>
@endsection
@section('robots')
    @parent
    <meta name="robots" content="noindex, nofollow"/>
@endsection
@section('content')
    <ul id="tabs-menu">
        <li class="iphone5"><a href="{{$url}}">О садике</a></li>
        <li class="tabmobile "><a rel="nofollow" href="{{$url}}/gallery">Фото <span
                    class="redtext">{{!empty($item->count_img) ? $item->count_img : 0}}</span></a></li>
        <li class="tabmobile active" ><a rel="nofollow" href="{{$url}}/agent">Руководство <span
                    class="redtext">{{!empty($item->user_id_agent) ? 1 : ''}}</span></a></li>
        <li><a rel="nofollow" href="{{$url}}/geoshow">Сады рядом <span
                    class="redtext">{{$item->nearby ?? 0}}</span></a></li>
        @if($item->ads_url)
            <li><a rel="nofollow" href="{{$item->ads_url}}">Обмен</a></li>
        @endif
    </ul>
    <div style="margin-bottom: 10px;">
        <div style="overflow: hidden;">
            @if(!empty($agent))
                Для данного садика уже зарегистрирован представитель - {{$agent->name}}
            @else
                <h1><a href="/registration-agent?item_id={{$item->id}}"
                   class="simplemodal" data-width="550" data-height="350">Зарегистрироваться</a></h1>
                <p>Эта страница предназначена для регистрации представителя садика у нас на сайте. При регистрации вы
                    должны указать e-mail, <b>который указан на официальном сайте</b>. Если модератор не найдет на
                    официальном сайте указанный e-mail - регистрация <b>будет отклонена</b>. На этот e-mail придет
                    ссылка для активации аккаунта. После регистрации будет доступно:</p>
                <ul>
                    <li>автоматические уведомления о новых отзывах</li>
                    <li>ответы пользователям от имени предстаивтеля садика</li>
                    <li>и самое главное, пользователи будут видеть, что администрация садика следит за отзывами и ей
                        не безразлично мнение родителей
                    </li>
                </ul>
            @endif
        </div>
    </div>
@endsection

