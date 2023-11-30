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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let form = document.getElementById('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Предотвращает стандартное поведение отправки формы
                let submitBtn = form.querySelector('input[type="submit"]');
                submitBtn.style.display = 'none';

                let xhr = new XMLHttpRequest();
                xhr.open('POST', '/post/error', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.responseType = 'json';
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let data = xhr.response;
                        let msgElement = document.getElementById('msg');

                        if (msgElement) {
                            msgElement.innerHTML = data.msg;
                        }

                        if (data.msg !== 'Ваше сообщение успешно отправлено') {
                            submitBtn.style.display = 'block';
                        }
                    }
                };

                xhr.send(new URLSearchParams(new FormData(form)).toString());
            });
        }
    });
</script>

<div id="msg" class="postmsg"></div>
<h3>Укажите ошибку</h3>
<form id="form" method="POST">
    @csrf
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="text" name="mailfrom" value="" placeholder="Ваш E-mail адрес" class="postform" required>
    <br>
    <textarea name="description" style="width: 95%;height: 200px" placeholder="Опишите где и что у нас неправильно" class="postform" required></textarea>
    <br>
    <input type="submit" name="submit" value="Отправить">
</form>

</body>
</html>
