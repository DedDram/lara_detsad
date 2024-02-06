<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex, nofollow" />
    <style>
        .postform {
            border: 2px solid #000;
            margin: 3px 0;
            padding: 3px;
        }
        .postmsg {
            margin-bottom: 7px;
            color: blue;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div id="msg" class="postmsg"></div>
@if (!empty($total) && $total>=8)
<div id="msg">К данному садику уже добавлено 8 фотографий, это максимум.</div>
@else
<form class="add-gallery" method="post" action="/post/add-gallery" enctype="multipart/form-data">
    @csrf
    <input type="file" name="myfile" style="width: 506px; margin-bottom: 4px;" required>
    <textarea name="description" style="width: 98%; height: 105px; margin: 10px 0; padding: 4px;border-color: #999;" class="postform" placeholder="Описание изображения" required></textarea><br>
    <input type="checkbox" name="condition" required> <span style="font-style: italic; color: #888;">Отправляя нам фотографии, Вы подтверждаете, что права на их использование принадлежат Вам, либо они получены из открытых источников и Вы не возражаете против публикации фото на нашем сайте</span>
    <br><br>
    <input type="hidden" name="item_id" value="{{$item_id}}">
    <input type="hidden" name="task" value="addGallery">
    <input type="submit" name="submit" value="Добавить фото"> <span class="percent" style="display: none; background: url('/images/loader.gif') left center no-repeat; padding-left: 20px;">0%</span>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.addEventListener("submit", function(e) {
            e.preventDefault();
            let form = e.target;
            ajaxSubmit(form);
        }, false);
    });
    function ajaxSubmit(form) {
        let xhr = new XMLHttpRequest();
        xhr.open(form.method, form.action, true);
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                let percentComplete = (event.loaded / event.total) * 100;
                form.querySelector('.percent').innerHTML = percentComplete + '%';
                form.querySelector('.percent').style.display = 'block';
            }
        };
        xhr.onload = function() {
            if (xhr.status === 200) {
                let data = JSON.parse(xhr.responseText);

                if (data.status === 1) {
                    showMessage('postmsg', data.msg);
                    form.style.display = 'none';
                } else if (data.status === 2) {
                    showMessage('postmsg', data.msg);
                }
                form.querySelector('.percent').innerHTML = '100%';
                form.querySelector('.percent').style.display = 'none';
                form.querySelector('.postform').style.backgroundColor = '#fff';
                form.querySelector('.postform').style.border = '1px solid #aaa';
                if (data.fields) {
                    data.fields.forEach(function(field) {
                        field.style.backgroundColor = '#fef1ec';
                        field.style.border = '1px solid #cd0a0a';
                    });
                }

                if (data.status > 0) {
                    scrollUp();
                }
            }
        };
        xhr.send(new FormData(form));
        function showMessage(className, message) {
            let msgElement = document.getElementById('msg');
            msgElement.className = className + ' ui-corner-all';
            msgElement.innerHTML = message;
            msgElement.style.display = 'block';
        }
    }
    function scrollUp() {
        let curPos = window.scrollY || document.documentElement.scrollTop;
        let scrollTime = curPos / 1.73;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endif
</body>
</html>
