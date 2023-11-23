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
    <div>
        <h1>Работа в детских садах {{ !empty($cityName) ? 'г.'.$cityName : '' }}</h1>
        <br>
        <p>Ниже представлены резюме соискателей и вакансии детских садов {{ !empty($cityName) ? 'г.'.$cityName : '' }}</p>
        <p>Все мы хоть раз сталкивались с проблемой поиска работы. На этой странице мы хотим помочь Вам найти работу в дошкольных учреждения.
            Соискатели могут добавить свое мини резюме, а работодатели - данные о свободной вакансии.</p>
        <span style="text-align: center"><a href="/rabota-add" class="simplemodal button" data-width="530" data-height="680">Добавить Резюме/Вакансию</a></span>
    </div>

    <br>
    <div style="margin-bottom: 32px;">
        <div style="float: left; margin-right: 14px;">
            <select name="type" id="type">
                <option value="0">- Вакансия/Резюме -</option>
                <option value="1">Вакансия (я предлагаю работу)</option>
                <option value="2">Резюме (я ищу работу)</option>
            </select>
        </div>
        <div style="float: left; margin-right: 14px;">
            <select id="teachers" class="inputbox" size="1">
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher['id'] }}">{{ $teacher['title'] }}</option>
                @endforeach
            </select>
        </div>
        <div style="float: left; margin-right: 14px;">
            <select id="citySelect" class="inputbox" size="1">
            @foreach ($city as $city_value)
                <option value="{{ $city_value['id'] }}">{{ $city_value['title'] }}</option>
            @endforeach
            </select>
        </div>
        <div id="mSelect" style="display: none; float: left; margin-right: 14px;">
            <select id="metroSelect" class="inputbox" size="1">
            @foreach ($metro as $metro_value)
                <option value="{{ $metro_value['id'] }}">{{ $metro_value['title'] }}</option>
            @endforeach
            </select>
        </div>
        <a href="#" id="search-rabota" style="float: left; margin-right: 14px;" class="button">Поиск</a>
    </div>
    <br>
    <br>

    <div id="popup-recaptcha" style="position: absolute;"></div>
    @if(!empty($items))
        @foreach($items as $item)
    <table>
        <tbody>
        <tr>
            <td style="width:92px">
                @if(!empty($item->photo))
                <img src="/images/rabota/{{$item->photo}}" style="width:80px">
                @else
                <img src="/images/stories/user.jpg" style="width:80px">
                @endif
            </td>
            <td>
                <strong>{{$item->teach}}</strong><br>
                @if($item->type > 1)
                <strong style="color:green;">Резюме</strong>
                @else
                <strong style="color:#2f56bd;">Вакансия</strong>
                @endif

                <i style="color:rgb(128,128,128);">добавлено: {{ \Carbon\Carbon::parse($item->created)->format('d.m.Y') }}
                </i><br>

                @if(!empty($item->text))
                    {{{$item->text}}}<br>
                @endif

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

