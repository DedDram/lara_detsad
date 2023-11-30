<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex, nofollow"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<script>
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    document.addEventListener('DOMContentLoaded', function () {
        let form = document.getElementById('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                let submitBtn = form.querySelector('input[type="submit"]');
                let citySelect = document.getElementById('citySelect');
                let metroSelect = document.getElementById('metroSelect');
                let hiddenMetro = document.getElementById('hiddenMetro');
                let hiddenCity = document.getElementById('hiddenCity');
                let city = citySelect.options[citySelect.selectedIndex].value;
                let metro = metroSelect.options[metroSelect.selectedIndex].value;

                // Устанавливаем значения скрытых полей city и metro
                hiddenCity.value = city;
                hiddenMetro.value = metro;
                submitBtn.style.display = 'none';

                let xhr = new XMLHttpRequest();
                xhr.open('POST', '/rabota-add', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.responseType = 'json';
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let data = xhr.response;
                        let msgElement = document.getElementById('msg');

                        if (msgElement) {
                            msgElement.innerHTML = data.msg;
                            form.remove();
                        }

                        if (data.msg !== 'Объявление будет опубликовано после проверки модератором') {
                            submitBtn.style.display = 'block';
                        }
                    }
                };

                xhr.send(new FormData(form));
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

<div id="msg" style="color: red; font-weight: bold;"></div>
<form id="form" method="POST" enctype="multipart/form-data">
    @csrf
    <h3>Добавить резюме/вакансию</h3>
    <div style="margin-bottom: 10px">
    <select name="type" id="type" required>
        <option value="0">- Вакансия/Резюме -</option>
        <option value="1">Вакансия (я предлагаю работу)</option>
        <option value="2">Резюме (я ищу работу)</option>
    </select>
    </div>
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

    <input type="text" name="email" id="email" placeholder="E-mail" class="ui-corner-all postform" style="width: 98%; margin-bottom: 2%; margin-top: 3%" required>

    <input type="text" name="username" id="fullname" placeholder="Контактное лицо" class="ui-corner-all postform" style="width: 98%; margin-bottom: 2%; margin-top: 3%" required>

    <div style="height: 10px;"></div>

    <label>Фотография</label>
    <input type="file" name="file" id="file">
    <div style="height: 10px;"></div>
    <label>Выберите предметы преподавания</label>
    <div style="overflow: auto; width: 98%; height: 150px; border: 2px solid #ccc;margin-bottom: 10px">
        @foreach($teachers as $teacher):
        <input type="checkbox" value="{{ $teacher['id'] }}" name="teacher[{{ $teacher['id'] }}]" /> {{ $teacher['title'] }}<br/>
        @endforeach
    </div>

    <label>Комментарии</label>
    <textarea name="text" style="width: 98%;height: 100px;"></textarea>
    <input type="hidden" name="city_id" id="hiddenCity" value="">
    <input type="hidden" name="metro_id" id="hiddenMetro" value="">
    <input type="submit" name="submit" id="submit" value="Добавить">
</form>

</body>
</html>
