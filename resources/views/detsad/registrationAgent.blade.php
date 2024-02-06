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
<div id="msg"></div>
<form method="post" action="{{ route('registrationAgentPost') }}">
    <p>При регистрации вы должны указать e-mail, который указан на официальном сайте детского саада.
    Если модератор не найдет на официальном сайте указанный e-mail - регистрация будет отклонена.</p>
    @csrf
     <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">ФИО (будет видно на этой странице)</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control " name="name" value="" required="" autocomplete="name" autofocus="">
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control " name="email" value="" required="" autocomplete="email">
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

            <div class="col-md-6">
                <input id="password" minlength="8" type="password" class="form-control " name="password" required="" autocomplete="new-password">
                @error('password')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Повторите пароль</label>

            <div class="col-md-6">
                <input id="password-confirm" minlength="8" type="password" class="form-control" name="password_confirmation" required="" autocomplete="new-password">
            </div>
        </div>
        <input type="hidden" name="item_id" value="{{$request->input('item_id')}}">
        <input type="hidden" name="agent" value="1">
    <br>
        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    Зарегистрироваться
                </button>
            </div>
        </div>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.addEventListener("submit", function(e) {
            e.preventDefault();

            let form = e.target;
            let formData = new FormData(form);

            fetch(form.action, {
                method: form.method,
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    let msgElement = document.getElementById('msg');
                    if (data.status === 1) {
                        msgElement.setAttribute('class', 'postmsg');
                        msgElement.innerHTML = data.msg;
                        form.style.display = 'none';
                    }
                    if (data.status === 2) {
                        msgElement.setAttribute('class', 'postmsg');
                        msgElement.innerHTML = data.msg;
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>
</body>
</html>
