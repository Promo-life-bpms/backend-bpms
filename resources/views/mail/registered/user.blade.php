@component('mail::message')
# ¡Buen día, {{ $data['name'] }}!
# Bienvenido al sistema BPMS - Promo Life - BH Trade Market
Tu información de inicio de sesión es:
<br>
Usuario: {{ $data['email'] }}
<br>
Contraseña: {{ $data['password'] }}
<br>
@component('mail::button', ['url' => $data['url'], 'color' => 'blue'])
    Inicia sesión
@endcomponent
<hr>
Si tienes problemas con el botón "Inicia sesión", dale click en el siguiente enlace: <a href="{{$data['url']}}">{{$data['url']}}</a>
@endcomponent
