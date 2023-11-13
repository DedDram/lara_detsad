@component('mail::message')
# {{ $subject }}
<p>{{ $message }}</p>

@component('mail::button', ['url' => $data['url'] ?? ''])
Перейти на DetskySad.com
@endcomponent

Если кнопа не работает, перейдите по ссылке {{ $data['url'] }}

@endcomponent
