<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $_SERVER['HTTP_HOST'] }}</title>
    <meta name='keywords' content='{{ $metaKey ?? $_SERVER['HTTP_HOST'] }}'/>
    <meta name='description' content='{{ $metaDesc ?? $_SERVER['HTTP_HOST'] }}'/>
    @section('styles')
        <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    @show
    @section('scripts')
    @show
    @section('robots')
    @show
</head>
<body>
<div class="old">
    <div class="container">
        <a href="https://detskysad.com/">
            <div class="logo">
                <div class="logo1">Отзывы</div>
                <div class="logo2">о детских садах</div>
            </div>
        </a>

        <div class="counter"><div class="count-inner"><p class="counter-title">Уже отзывов</p>
                <div class="all-count">
                    <p class="response" id="response">{{$allReviews}}</p></div>
            </div>
        </div>
        <div class="login">
        </div>
        <div class="composition"></div>
        <div class="clear-fix"></div>
        <div class="poisk">
            <div class="yandex-search">
                <div class="ya-site-form ya-site-form_inited_no" onclick="return {'action':'https://detskysad.com/search','arrow':false,'bg':'transparent','fontsize':12,'fg':'#000000','language':'ru','logo':'rb','publicname':'Yandex Site Search #577793','suggest':true,'target':'_self','tld':'ru','type':2,'usebigdictionary':true,'searchid':577793,'input_fg':'#000000','input_bg':'#ffffff','input_fontStyle':'normal','input_fontWeight':'normal','input_placeholder':'поиск по сайту','input_placeholderColor':'#999999','input_borderColor':'#7f9db9'}">
                    <form action="https://yandex.ru/search/site/" method="get" target="_self">
                        <table>
                            <tr>
                                <td class="yandex-search-text">
                                    <input name="searchid" type="hidden" value="577793" />
                                    <input name="l10n" type="hidden" value="ru" />
                                    <input name="reqenc" type="hidden" value="" />
                                    <input name="text" type="search" value="" placeholder="поиск по сайту"/>
                                </td>
                                <td class="yandex-search-submit">
                                    <input type="submit" value="Найти" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <a class="toggleMenu" href="#">☰</a>
    <div id="menu2">

    </div>
    <div class="clear-fix"></div>
    <div class="menubg"></div>
    <div class="container">
        <div id="content">
            <div class="breadcrumb">
                @yield('breadcrumb')
            </div>
            @yield('message')
            @if(session('success'))
                <div class="alert alert-message">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
    <div class="clear-fix"></div>
    <footer>
        <div class="bg-footer"></div>
        <div class="container">
            @yield('debug')
        </div>
        <script type="text/javascript" >
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function() {
                    try {
                        w.yaCounter11397493 = new Ya.Metrika({
                            id:11397493,
                            clickmap:true,
                            trackLinks:true,
                            accurateTrackBounce:true
                        });
                    } catch(e) { }
                });
                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () { n.parentNode.insertBefore(s, n); };
                s.type = "text/javascript";
                s.async = true;
                s.src = "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/watch.js";
                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else { f(); }
            })(document, window, "yandex_metrika_callbacks");
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/11397493" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    </footer>
</div>
</body>
</html>
