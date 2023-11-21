<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex, nofollow"/>
</head>
<body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
       let form = document.getElementById('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                let submitBtn = form.querySelector('input[type="submit"]');
                let citySelect = document.getElementById('citySelect');
                let metroSelect = document.getElementById('metroSelect');
                let hiddenMetro = document.getElementById('hiddenMetro');
                let cityValue = this.value;
                let hiddenCity = document.getElementById('hiddenCity');
                hiddenCity.value = cityValue;
                let city = citySelect.options[citySelect.selectedIndex].value;
                let metro = metroSelect.options[metroSelect.selectedIndex].value;

                // Устанавливаем значения скрытых полей city и metro
                hiddenCity.value = city;
                hiddenMetro.value = metro;
                submitBtn.style.display = 'none';

                let xhr = new XMLHttpRequest();
                xhr.open('POST', '/obmen-add', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.responseType = 'json';
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let data = xhr.response;
                        let msgElement = document.getElementById('msg');

                        if (msgElement) {
                            msgElement.innerHTML = data.msg;
                        }

                        if (data.msg !== 'Ваше объявление будет добавлено в течение 5 минут') {
                            submitBtn.style.display = 'block';
                        }
                    }
                };

                xhr.send(new URLSearchParams(new FormData(form)).toString());
            });
        }

        document.getElementById('citySelect').addEventListener('change', function () {
            var cityValue = this.value;
            var mSelect = document.getElementById('mSelect');

            if (cityValue === '4-moskva') {
                mSelect.style.display = 'block';
            }
        });

    });
</script>

<div id="msg" class="postmsg"></div>
<h3>Укажите ошибку</h3>
<form id="form" method="POST">
    @csrf
    <div>
        <select id="citySelect" class="inputbox" size="1">
            @foreach ($city as $item)
                <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
            @endforeach
        </select>
    </div>

    <div id="mSelect" style="display: none; margin-top: 2%">
        <select id="metroSelect" class="inputbox" size="1">
            @foreach ($metro as $value)
                <option value="{{ $value['id'] }}">{{ $value['title'] }}</option>
            @endforeach
        </select>
    </div>
    <input type="text" name="phone" id="phone" placeholder="Телефон" class="ui-corner-all postform" style="width: 98%; margin-bottom: 2%; margin-top: 3%" required>
    <input type="text" name="email" id="email" placeholder="E-mail" class="ui-corner-all postform" style="width: 98%; margin-bottom: 2%" required>
    <br>
    <input type="text" name="username" id="username" placeholder="Контактное лицо" class="ui-corner-all postform" style="width: 98%; margin-bottom: 2%" required>

    <div style="height: 10px;"></div>

    <label>Укажите какой садик Вы предлагаете к обмену и на какой хотите поменять.</label>
    <textarea name="text" class="ui-corner-all" style="width: 98%;height: 100px;" required></textarea>
    <input type="hidden" name="city_id" id="hiddenCity" value="">
    <input type="hidden" name="metro_id" id="hiddenMetro" value="">
    <input type="submit" name="submit" id="submit" value="Добавить">
</form>

</body>
</html>
