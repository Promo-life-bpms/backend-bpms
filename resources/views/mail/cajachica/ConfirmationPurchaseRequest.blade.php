<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Entrega de Efectivo</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            position: relative;
        }
        .inner-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .bpms-legend {
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            line-height: 1.5em;
            margin-top: 0;
            color: #b0adc5;
            font-size: 12px;
            text-align: center;
        }
        h1, h2, h3 {
            color: #333;
        }
        p {
            margin-bottom: 20px;
        }
        /* Fondo azul detrás del contenedor */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(241, 241, 241);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="inner-container">
            <h1>¡Buen día, {{ $receptor }}!</h1>
            <br>
            <div style="text-align: justify;">
                {{ $emisor }} ha confirmado la entrega de efectivo de la solicitud "GTO-{{$idPurchase}}".
                Apartir de ahora puedes pasar con el personal de Caja chica para la entrega de dinero. 
            </div>
            <br>
            <div style="text-align: justify;">
                Recuerda que una vez hayas recibido el dinero, deberás confirmar su recepción en el sistema. 
                De lo contrario, ya no se te volverá a entregar efectivo hasta que hablemos con tu jefe directo.
            </div>
        </div>
        <div class="bpms-legend">
            BPMS PL - BH. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>