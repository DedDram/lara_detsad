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
    @if (Auth::check())
        @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isAgent()))
            <div id="system-message">
                <div class="alert alert-message">
                    <div>
                            @if(session('removeImgOk'))
                                <div class="alert-message">{{ session('removeImgOk') }}</div>
                            @elseif (session('removeImgError'))
                                <div class="alert-message">{{ session('removeImgError') }}</div>
                            @elseif (session('publishImgOk'))
                                <div class="alert-message">{{ session('publishImgOk') }}</div>
                            @elseif (session('publishImgError'))
                                <div class="alert-message">{{ session('publishImgError') }}</div>
                            @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
    <!--beforecontent-->

    <ul id="tabs-menu">
        <li class="iphone5"><a href="{{$url}}">О садике</a></li>
        <li class="tabmobile active "><a rel="nofollow" href="{{$url}}/gallery">Фото <span
                    class="redtext">{{!empty($item->count_img) ? $item->count_img : 0}}</span></a></li>
        <li class="tabmobile"><a rel="nofollow" href="{{$url}}/agent">Руководство <span
                    class="redtext">{{!empty($item->user_id_agent) ? 1 : ''}}</span></a></li>
        <li><a rel="nofollow" href="{{$url}}/geoshow">Сады рядом <span
                    class="redtext">{{$item->nearby ?? 0}}</span></a></li>
        @if($item->ads_url)
            <li><a rel="nofollow" href="{{$item->ads_url}}">Обмен</a></li>
        @endif
    </ul>

    <div style="margin-bottom: 10px;">
        @if (!$gallery->isEmpty())
            @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isAgent()))
        Удалить фото можно нажав на красный крестик в правом углу каждой фотографии.<br>
        Вновь добавленные фото будут отображаться в том же порядке, в каком вы их и добавили.<br>
        Максимально кол-во фото - 8 шт.<br><br>
        @endif
        <div class="popup-gallery">
                @foreach ($gallery as $image)
            <div style="position: relative; display: inline-block;">
                <a href="{{ asset("/images/detsad/{$item->id}/{$image->original_name}") }}" title="{{ $image->title }}" class="simplemodal" data-width="800" data-height="500">
                    <img src="{{ asset("/images/detsad/{$item->id}/{$image->thumb}") }}" alt="{{ $image->title }}">
                </a>
                @if (Auth::check() && (Auth::user()->isAdmin() || (Auth::user()->isAgent() && Auth::user()->sad_id == $item->id)))
                <a href="/remove-image-gallery?id={{$item->id}}&original_name={{$image->original_name}}" class="remove-link">
                    <img src="/images/del.png" style="position: absolute; top: 0; right: 0;width: 20px;height: 20px;" class="remove-img" alt="delete">
                </a>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p>Фотоальбом пуст (загружайте фото весом не более 2 мб., максимум 8 фото)</p>
        @endif
        @if(count($gallery) < 8)
        <a href="/gallery-add?id={{$item->id}}" class="simplemodal button"
           data-width="550" data-height="350">Добавить новое фото</a>
        @endif
        <div style="padding-top: 9px; text-align: right;">
            <div style="padding-bottom: 5px; text-align: left;padding-top: 5px;">
                <a href="/post/error?id={{$item->id}}"
                   class="simplemodal find_error_btn" data-width="450" data-height="430"
                   style="vertical-align: middle;">Нашли ошибку?</a>
            </div>
        </div>
    </div>
@endsection

