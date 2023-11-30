@extends('layouts')
@section('meta')
    @parent
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('styles')
    @parent
    <link href="{{ asset('/css/sadik.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/comments.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/comments.form.css') }}" rel="stylesheet">
@endsection
@section('scripts')
    @parent
    <script src="{{ mix('/js/map-sadik.js') }}" defer></script>
    <script src="{{ mix('/js/recaptcha.js') }}" defer></script>
    <script src="{{ mix('/js/simpleModal.js') }}" defer></script>
    <script src="{{ mix('/js/comments.js') }}" defer></script>
    @if(Auth::check() && Auth::user()->isAdmin())
        <script src="{{ asset('/js/moderation.js') }}" defer></script>
    @endif
@endsection
@section('content')
    @if (Auth::check() && (session('unpublish') || session('publish') || session('remove') || session('blacklist') || session('unsubscribe')))
        @if (Auth::user()->isAdmin())
            <div id="system-message">
                <div class="alert alert-message">
                    <div>
                        @if(session('unpublish'))
                            <div class="alert-message">{{ session('unpublish') }}</div>
                        @elseif (session('publish'))
                            <div class="alert-message">{{ session('publish') }}</div>
                        @elseif (session('remove'))
                            <div class="alert-message">{{ session('remove') }}</div>
                        @elseif (session('blacklist'))
                            <div class="alert-message">{{ session('blacklist') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        @if (session('unsubscribe'))
            <div id="system-message">
                <div class="alert alert-message">
                    <div>
                        <div class="alert-message"> {{ session('unsubscribe') }}</div>
                    </div>
                </div>
            </div>
        @endif
    @endif
    <div itemscope itemtype="http://schema.org/School">
        <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"
             style="margin-top: 5px; text-align: center; font-size: 14px; font-weight: bold; color: #6c0; overflow: hidden;">
            <div style="float: left; margin: 8px 5px 0 0;">Рейтинг: <span
                    itemprop="ratingValue">{{round($item->average, 1)}}</span>
            </div>
            <div class="stars" style="float: left;"><label></label><span style="width: {{$item->average*20}}%"></span>
            </div>
            <div style="float: left; margin: 8px 0 0 5px;"><span
                    itemprop="ratingCount">{{$ratingCount[0]}}</span> {{$ratingCount[1]}}
            </div>
            <meta itemprop="itemReviewed" content="{{$item->name}}">
            <meta itemprop="worstRating" content="1"/>
            <meta itemprop="bestRating" content="5"/>
        </div>
        <a href="#" class="map-wrapper-toogle" id="mapToggle">Открыть карту</a>
        <div class="map-wrapper" id="mapWrapper">
            <div id="map-container" style="width: 100%; height: 200px;display: none"></div>
        </div>
        @if (!empty($statistics->infoUp))
            <table class="table-3">
                <thead>
                <tr>
                    <th>Данные на</th>
                    <th>Бюджет садика</th>
                    <th>Кол-во работников</th>
                    <th>Средняя зарплата</th>
                    <th>Кол-во детей</th>

                </tr>
                </thead>
                <tbody>
                <tr>
                    @php
                        if (strlen($statistics->salary) == 5) {
                            $statistics->salary = preg_replace('~(\d{2})(\d{3})~m', '$1.$2', $statistics->salary);
                        } elseif (strlen($statistics->salary) == 6) {
                            $statistics->salary = preg_replace('~(\d{3})(\d{3})~m', '$1.$2', $statistics->salary);
                        }
                        if (strlen($statistics->budget) == 7) {
                            $statistics->budget = preg_replace('~(\d{1})(\d{3})(\d{3})~m', '$1.$2.$3', $statistics->budget);
                        } elseif (strlen($statistics->budget) == 8) {
                            $statistics->budget = preg_replace('~(\d{2})(\d{3})(\d{3})~m', '$1.$2.$3', $statistics->budget);
                        }elseif (strlen($statistics->budget) == 9) {
                            $statistics->budget = preg_replace('~(\d{3})(\d{3})(\d{3})~m', '$1.$2.$3', $statistics->budget);
                        }elseif (strlen($statistics->budget) == 10) {
                            $statistics->budget = preg_replace('~(\d{1})(\d{3})(\d{3})(\d{3})~m', '$1.$2.$3.$4', $statistics->budget);
                        }
                        $statistics->budget = $statistics->budget ?: '?';
                        $statistics->workers = $statistics->workers ?: '?';
                        $statistics->salary =  $statistics->salary ?: '?';
                        $statistics->students =  $statistics->deti ?: '?';
                    @endphp
                    <td data-label="Данные на">{{$statistics->infoUp}} г.</td>
                    <td data-label="Бюджет садика">{{$statistics->budget}} руб.</td>
                    <td data-label="Кол-во работников">{{$statistics->workers}}</td>
                    <td data-label="Средняя зарплата">{{$statistics->salary}} руб.</td>
                    <td data-label="Кол-во детей">{{$statistics->deti}}</td>
                </tr>
                </tbody>
            </table>
        @endif


        <script type="text/javascript">
            window.address = @json($addresses);
        </script>

        {{$item->text ?? ''}}
        <h1 itemprop="name">{{$item->header ?? ''}}</h1>
        <ul id="tabs-menu">
            <li class="active iphone5"><a href="{{$url}}">О садике</a></li>
            <li class="tabmobile"><a rel="nofollow" href="{{$url}}/gallery">Фото <span
                        class="redtext">{{!empty($item->count_img) ? $item->count_img : 0}}</span></a></li>
            <li class="tabmobile"><a rel="nofollow" href="{{$url}}/agent">Руководство <span
                        class="redtext">{{!empty($item->user_id_agent) ? 1 : ''}}</span></a></li>
            <li><a rel="nofollow" href="{{$url}}/geoshow">Сады рядом <span
                        class="redtext">{{$item->nearby ?? 0}}</span></a></li>
            @if($item->ads_url)
                <li><a rel="nofollow" href="{{$item->ads_url}}">Обмен</a></li>
            @endif
        </ul>

        <div style="margin-bottom: 10px;" id="data">
            <div style="overflow: hidden;">
                @if(!empty($item->preview_src))
                    <span itemscope itemtype="https://schema.org/ImageObject"><img class="preview-border"
                                                                                   style="width: {{$widthImage}}px;height: {{$heightImage}}px"
                                                                                   alt="{{$item->header}}"
                                                                                   title="{{$item->header}}"
                                                                                   src="/images/detsad/{{$item->id}}/{{$item->preview_src}}"
                                                                                   itemprop="contentUrl"></span>
                @else
                    <span class="zaglushka">
                            <a href="/add-photo?item_id={{$item->id}}&task=photo"
                               class="simplemodal" data-width="450" data-height="430">
                            <img class="foto" style="float: right;width: {{$widthImage}}px;height: {{$heightImage}}px" src="/images/detsad.jpg">
                            </a>
                            </span>
                @endif
                <p>{{$item->affiliation}}</p>
                @foreach($addresses as $address)
                    <div style="margin-bottom: 13px;">
                        <span style="color: #E86500;font-weight: bold;">Адрес:</span> <span
                            itemprop="address">{{$address->geo_code}}</span>
                    </div>
                @endforeach
                @foreach($addresses as $address)
                    @if (!empty($address->district_link))
                        <div style="margin-bottom: 13px;">
                            <span style="color:#E86500;font-weight: bold;">Округ/район:</span> {!! $address->district_link !!}
                        </div>
                            @break
                    @endif
                @endforeach

                @foreach($fields as $field)
                    @if (!empty($field->field_text))
                        @if ($field->type_id == 5)
                            @if ($field->field_text == 'нет данных')
                                <div style="margin-bottom: 13px;">
                                    <span style="color: #E86500;font-weight: bold;">{{$field->type_text}}:</span> нет
                                    данных
                                </div>
                            @else
                                <div style="margin-bottom: 13px;">
                                    <span style="color: #E86500;font-weight: bold;">{{$field->type_text}}:</span> <a
                                        rel="nofollow" target="_blank" itemprop="url"
                                        href="https://detskysad.com/index.php?redirect={{urlencode($field->field_text)}}"
                                        title="http://{{$field->field_text}}">{{$field->field_text}}</a>
                                </div>
                            @endif
                        @else
                            @if ($field->type_id == 1)
                                <div style="margin-bottom: 13px;">
                                    <span style="color: #E86500;font-weight: bold;">{{$field->type_text}}:</span> <span
                                        id="show-mail"> {{substr($field->field_text, 0, 7)}}...<a rel="nofollow"
                                                                                                  data-id="{{$field->item_id}}"
                                                                                                  data-type="{{$field->type_id}}"
                                                                                                  href="#">показать</a></span>
                                    <div id="popup-recaptcha"></div>
                                </div>
                            @elseif($field->type_id == 6)
                                <div style="margin-bottom: 13px;">
                                    <span
                                        style="color: #E86500;font-weight: bold;">{{$field->type_text}}:</span>
                                    <span
                                        id="show-telephone"
                                        itemprop="telephone"> {{substr($field->field_text, 0, 7)}}... <a
                                            rel="nofollow" data-id="{{$field->item_id}}"
                                            data-type="{{$field->type_id}}" href="#">показать</a></span>
                                    <div id="popup-recaptcha-telephone"></div>
                                </div>
                            @elseif($field->type_id == 2)
                                <div style="margin-bottom: 13px;">
                                    <span style="color: #E86500;font-weight: bold;">
                                         @if (mb_substr(trim($field->field_text), -1) == "а")
                                            {{$field->type_text}}
                                        @else
                                            Руководитель:
                                        @endif
                                    </span> {{$field->field_text}}
                                </div>
                            @else
                                <div style="margin-bottom: 13px;">
                                    <span
                                        style="color: #E86500;font-weight: bold;">{{$field->type_text}}:</span> {!! $field->field_text !!}
                                </div>
                            @endif
                        @endif
                    @endif
                @endforeach
                    <div style="margin-bottom: 13px;">
                        <span style="color: #E86500; font-weight: bold;">Диплом для сайта садика:</span>
                        @if (in_array($item->section_id, array(1, 2, 17, 20, 21)))
                        <span class="top_detsad_diploma_cell"><a
                                href="/diplom/code?id={{$item->id}}"
                                title="Получить диплом" class="simplemodal" data-width="580" data-height="680"></a></span> (по
                        стране), <span class="top_detsad_diploma_cell"><a
                                href="/diplom/code?city=1&id={{$item->id}}"
                                title="Получить диплом" class="simplemodal" data-width="580" data-height="680"></a></span> (по
                        городу)
                        @endif
                        @if (in_array($item->section_id, array(3, 4, 5, 6, 7, 8, 9, 10, 11)))
                        <span class="top_detsad_diploma_cell"><a
                                href="/diplom/code?city=1&id={{$item->id}}"
                                title="Получить диплом" class="simplemodal" data-width="580" data-height="680"></a></span> (по
                        городу), <span class="top_detsad_diploma_cell"><a
                                href="/diplom/code?district=1&id={{$item->id}}"
                                title="Получить диплом" class="simplemodal" data-width="580" data-height="680"></a></span> (по
                        району)
                        @endif
                        @if ($item->section_id == 14)
                        <span class="top_detsad_diploma_cell"><a
                                href="/diplom/code?district=1&topx-id={{$item->id}}"
                                title="Получить диплом" class="simplemodal" data-width="580" data-height="680"></a></span> (по
                        округу)
                        @endif
                    </div>
            </div>
            <div id="panel-m"></div>
            <div id="yaMiddle">
            <div id="yandex_rtb_R-A-66213-2"></div>
            <script>
                window.yaContextCb.push(()=>{
                    Ya.Context.AdvManager.render({
                        "blockId": "R-A-66213-2",
                        "renderTo": "yandex_rtb_R-A-66213-2"
                    })
                })
            </script>
            </div>
            <div style="padding-top: 9px; text-align: left;">
                <a href="/post/error?id={{$item->id}}"
                   class="simplemodal find_error_btn" data-width="450" data-height="430"
                   style="vertical-align: middle;">Ошибка в описании?</a>
                <span
                    class="scomments-date created">Данные обновлены: {{explode(' ', $item->modified)[0]}}</span><br>

            </div>
        </div>

        <div itemscope itemtype="https://schema.org/Review">
            <meta itemprop="itemReviewed" itemscope itemtype="https://schema.org/Organization">
            <meta itemprop="itemReviewed" content="{{$item->name}}">
            <x-comments
                :object_group="$object_group"
                :object_id="$item->id"
                :items="$items"
                :countComments="$countComments"
                :good="$good"
                :neutrally="$neutrally"
                :bad="$bad"
                :procentGood="$procentGood"
                :procentNeutrally="$procentNeutrally"
                :procentBad="$procentBad"
                :modulePosition="$modulePosition"
                :num="$num"
                :user="$user"
            />
        </div>
    </div>
@endsection

