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
    <script src="{{ mix('/js/recaptchaAdsAndSelect.js') }}" defer></script>
@endsection
@section('content')
    <!--beforecontent-->
    @if(empty($cityName))
        <div>
            Портал DetskySad.com предлагает посетителям уникальную возможность. Здесь вы найдете объявления реальных
            людей по обмену путевок в детские садики. Что это такое и для чего нужно? Все просто!
            Чтобы попасть в дошкольное учреждение, нужно получить путевку. Ребенку после сбора всех справок,
            собеседования заранее специально выделяют место в садике, расположенном в конкретном городе, округе и
            районе. Оно закреплено за ним. Но обстоятельства часто меняются. Возможно, семья резко соберется переехать в
            другой район или округ российской столицы, а то и вовсе город. Могут измениться условия в самом саду:
            уволится логопед, перестанут проводить конкретные занятия, нужные именно вашему ребенку, перейдет на другую
            работу любимый воспитатель. Иногда становится трудно добираться до учреждения, некому возить малыша, нет
            машины, а на городском транспорте не успеваете. Что делать в таких ситуациях?
            Во-первых, не паниковать. Во-вторых, обратите внимание на раздел нашего сайта, посвященный обмену путевок.
            Иногда намного проще поменяться местами, чем искать новое, ведь, как правило, все места уже укомплектованы,
            и найти свободное значит чуть ли не выиграть в лотерею. Сами понимаете, насколько малы шансы.
            Благодаря же объявлениям, опубликованным на сайте, появляется возможность решить проблему относительно
            быстро и легко. Вы можете наткнуться на привлекательные для вас условия или рассказать о своем предложении и
            предпочтительных для вас вариантах. Автором объявления может стать житель любого российского города, а не
            только столицы. Даже больше, вы смело можете рассчитывать на обмен, даже если живете в Беларуси, Украине или
            другой стране СНГ.
            Достаточно просто регулярно просматривать сообщения в разделе и отвечать на полученные предложения, если
            разместили на сайте свое объявление.
        </div>
        <p><span style="color: #ff0000;"><strong>Внимание!!! Зафиксированы попытки мошенничества, вам могут позвонить и попытаться выманить деньги под предлогом обмена садиком. Будьте осторожны!</strong></span>
        </p>
    @endif

    <div>
        <h1>Обмен местами (путевок) в детские сады
            @if(!empty($metroName))
                {{' у метро '.$metroName}}
            @endif
            @if(!empty($cityName))
                {{' г. '.$cityName}}
            @endif
        </h1>

        <br>
        <p>Часто родители сталкиваются с проблемой, когда место для ребенка в детском саду уже выделили,
            а садик не подходит или нужно переезжать, да мало ли что. На этой странице мы постарались собрать
            объявления об обмене местами в детских садах города
            @if(!empty($cityName))
                {{$cityName}}
            @endif
            .
            Добавляйте свои объявления, описывайте куда хотите поменяться и возможно,
            Вам ответят заинтересованные родители.</p>
        <span style="text-align: center"><a href="/obmen-add" class="simplemodal button" data-width="530"
                                            data-height="420">Добавить объявление</a></span>
    </div>

    <br>
    <div style="margin-bottom: 32px;">
        <div style="float: left; margin-right: 14px;">
            <select id="citySelect" class="inputbox" size="1">
                @foreach ($city as $item)
                    <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                @endforeach
            </select>
        </div>

        <div id="mSelect" style="display: none; float: left; margin-right: 14px;">
            <select id="metroSelect" class="inputbox" size="1">
                @foreach ($metro as $value)
                    <option value="{{ $value['id'] }}">{{ $value['title'] }}</option>
                @endforeach
            </select>
        </div>
        <a href="#" id="search-obmen" style="float: left; margin-right: 14px;" class="button">Поиск</a>
    </div>
    <br>

    <div id="popup-recaptcha" style="position: absolute;"></div>
    @if(!empty($items))
        @foreach($items as $item)
            <table style="width:85%;">
                <tbody>
                <tr>
                    <td>
                        <strong style="color:green;">Объявление</strong>
                        <i style="color:rgb(128,128,128);">добавлено: {{ \Carbon\Carbon::parse($item->created)->format('d.m.Y') }}
                        </i><br>
                        {{$item->text}}<br>

                        @if(!empty($item->phone))
                            <b> Телефон:</b> <span class="show-popup-recaptcha">
                            {{substr($item->phone, 0, 7)}}...<a rel="nofollow" data-id="{{$item->id}}"
                                                                data-task="phoneAds" href="#">показать</a></span><br>
                        @endif

                        @if(!empty($item->fullname))
                            <b> Контактное лицо:</b> {{$item->fullname}}
                            @if(!empty($item->email))
                                (<span class="show-popup-recaptcha"><a rel="nofollow" data-id="{{$item->id}}"
                                                                       data-task="mailAds" href="#">показать e-mail</a></span>
                                )
                            @endif
                            <br>
                        @endif

                        @if(!empty($item->metro_name))
                            <b> Ближайшее метро:</b> {{$item->metro_name}}<br>
                        @endif

                        @if(!empty($item->city_name))
                            <b> Город:</b> {{$item->city_name}}<br>
                        @endif

                    </td>
                </tr>
                </tbody>
            </table>

            <hr>
        @endforeach
        <br>
        @if(!empty($items))
            {{ $items->links('vendor.pagination.custom-pagination') }}
        @endif
    @else
        Объявлений нет
    @endif
@endsection

