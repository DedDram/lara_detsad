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
                xhr.open('POST', '/add-photo', true);
                xhr.responseType = 'json';
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let data = xhr.response;
                        let msgElement = document.getElementById('msg');

                        if (msgElement) {
                            msgElement.innerHTML = data.msg;
                        }

                        if (data.msg !== 'Файл успешно загружен, скоро мы обновим логотип садика') {
                            submitBtn.style.display = 'block';
                        }
                    }
                };

                let formData = new FormData(form); // Создаем объект FormData для отправки данных формы
                xhr.send(formData);
            });
        }
    });
</script>

<div id="msg" class="postmsg"></div>
<h3>Добавить фото</h3>
<form id="form" method="POST">
    @csrf
    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
    <input type="hidden" name="task" value="photo">
    <input type="text" name="mailfrom" value="" placeholder="Ваш E-mail адрес" class="postform" required>
    <br> <br>
    <label for="file">Выберите файл</label> <br> <br>
    <input type="file" name="file" id="file" required>
    <br> <br>
    <input type="submit" name="submit" value="Отправить">
</form>

</body>
</html>
