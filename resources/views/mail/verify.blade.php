@component('mail::message')

<p>Нажмите на кнопку ниже, чтобы подтвердить почту.</p>

@component('mail::button', ['url' => $url])
Подтвердить почту
@endcomponent

Если кнопка не работает, перейдите по ссылке {{ $url }}

@endcomponent
