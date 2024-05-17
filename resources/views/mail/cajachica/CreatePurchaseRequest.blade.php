@component('mail::message')
# ¡Buen día, {{ $receptor}}!

{{ $emisor }} ha creado una nueva solicitud de gasto. 
El No. de la solicitud es "GOT-{{$idPurchase}}"
@endcomponent
