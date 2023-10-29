@component('mail::message')
# {{ $subject }}
<p>{{ $message }}</p>

@component('mail::button', ['url' => $data['url'] ?? ''])
Перейти на V-U-Z.ru
@endcomponent

Если кнопа не работает, перейдите по ссылке {{ $data['url'] }}

@endcomponent
