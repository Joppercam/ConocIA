<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background:#f1f5f9; margin:0; padding:20px; }
    .wrapper { max-width:560px; margin:0 auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .header { background:linear-gradient(135deg,#0a1020,#16213e); padding:28px 32px; }
    .header h1 { color:#fff; font-size:1.1rem; margin:0; font-weight:700; }
    .header span { color:#38b6ff; }
    .body { padding:28px 32px; }
    .meta { background:#f8fafc; border-radius:8px; padding:16px 20px; margin-bottom:20px; }
    .meta p { margin:4px 0; font-size:.88rem; color:#475569; }
    .meta strong { color:#1e293b; }
    .message { font-size:.95rem; color:#334155; line-height:1.7; border-left:3px solid #38b6ff; padding-left:14px; }
    .footer { padding:16px 32px; background:#f8fafc; font-size:.78rem; color:#94a3b8; text-align:center; border-top:1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1><span>ConocIA</span> — Mensaje de contacto</h1>
    </div>
    <div class="body">
        <div class="meta">
            <p><strong>De:</strong> {{ $senderName }} &lt;{{ $senderEmail }}&gt;</p>
            <p><strong>Asunto:</strong> {{ $subject }}</p>
            <p><strong>Fecha:</strong> {{ now()->locale('es')->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        </div>
        <div class="message">
            {!! nl2br(e($messageBody)) !!}
        </div>
    </div>
    <div class="footer">
        Este mensaje fue enviado desde el formulario de contacto de <strong>conocia.cl</strong>.<br>
        Para responder, usá la dirección de email del remitente.
    </div>
</div>
</body>
</html>
