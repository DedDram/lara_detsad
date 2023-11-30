<?php

function show_vars()
{
    $vars = array('country', 'region', 'city', 'district', 'id');
    $query = array();
    foreach ($vars as $var) {
        if (isset($_GET[$var])) {
            $query[$var] = $_GET[$var];
        }
    }
    echo http_build_query($query);
}
?>
    <!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex, nofollow">
</head>
<body>

<style>
    body {
        font-family: Tahoma;
        color: #333
    }

    #diploma_demo, textarea {
        width: 90%
    }
</style>
HTML-код для вставки диплома на ваш сайт:<br/>
<textarea>
<script type="text/javascript" src="https://detskysad.com/diplom/code?<?php show_vars(); ?>"></script>
</textarea>
<br/>
<span style="font-size:12px">Этот диплом автообновляем. Если место садика в рейтинге измениться, то и информация на дипломе автоматически поменяется, без вашего участия.
Рейтинг садика можно изменить написав про него отзыв.</span>
<br/>

Результат:
<div id="diploma_demo">
    <script type="text/javascript"
            src="https://detskysad.com/diplom/default?<?php show_vars(); ?>&topx-size=300"></script>
</div>
</body>
</html>
