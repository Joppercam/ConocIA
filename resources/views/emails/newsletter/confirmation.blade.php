<x-mail::message>
# Confirma tu suscripción a ConocIA

Gracias por suscribirte a **ConocIA**, el portal de noticias sobre Inteligencia Artificial en español.

Para completar tu suscripción y comenzar a recibir las últimas noticias de IA, haz clic en el botón:

<x-mail::button :url="$confirmationUrl" color="blue">
Confirmar suscripción
</x-mail::button>

Este enlace expirará en **48 horas**.

Si no solicitaste esta suscripción, puedes ignorar este correo o [cancelarla aquí]({!! $unsubscribeUrl !!}).

Gracias,<br>
El equipo de **{{ config('app.name') }}**
</x-mail::message>
