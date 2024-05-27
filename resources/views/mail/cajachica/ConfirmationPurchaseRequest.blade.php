@component('mail::message')
# ¡Buen día, {{ $receptor}}!

{{ $emisor }} ha confirmato la entrega de efectivo de la solicitud "GTO-{{$idPurchase}}".
Apartir de ahora puedes pasar con el personal de caja chica para la entrega de dinero. 

Recuerda que una vez hayas recibido el dinero, deberás confirmar su recepción en el sistema. 
De lo contrario, ya no se te volverá a entregar efectivo hasta que hablemos con tu jefe directo.
@endcomponent