<?php
/**
 * Script para enviar correo de simulación.
 *
 * INSTRUCCIONES PARA EL HOSTING:
 * 1. Sube este archivo a tu servidor Hostinger (ej: public_html/enviar_correo.php)
 * 2. Cambia el correo en la variable $remitente por el correo que creaste en Hostinger (ej: contacto@tudominio.com).
 * 3. En tu archivo index.html, asegúrate de actualizar la variable urlPhpCorreo apuntando a donde subiste este archivo.
 */

// 1. CONFIGURA TU CORREO AQUÍ
$remitente = "clientes@proyectaconstructora.com"; // <-- CAMBIAR POR TU CORREO DE HOSTINGER
$nombre_remitente = "Asesoría Inmobiliaria";

// Configurar encabezados CORS para permitir peticiones desde cualquier dominio
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido. Utiliza POST."]);
    exit;
}

// Leer datos JSON del cuerpo de la petición
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos inválidos o no recibidos."]);
    exit;
}

// Extraer datos del cliente
$cliente_nombre = htmlspecialchars($data['cliente_nombre'] ?? 'Cliente');
$cliente_email = filter_var($data['cliente_email'] ?? '', FILTER_VALIDATE_EMAIL);
$cliente_tel = htmlspecialchars($data['cliente_tel'] ?? 'N/A');
$cliente_dir = htmlspecialchars($data['cliente_dir'] ?? 'N/A');

if (!$cliente_email) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Correo electrónico del cliente no proporcionado o inválido."]);
    exit;
}

// Extraer datos de simulación
$lote_nombre = htmlspecialchars($data['lote_nombre'] ?? 'Lote');
$lote_area = htmlspecialchars($data['lote_area'] ?? 'N/A');
$valor_total = htmlspecialchars($data['valor_total'] ?? '$0');
$cuota_inicial = htmlspecialchars($data['cuota_inicial'] ?? '$0');
$meses = htmlspecialchars($data['meses'] ?? '1');
$saldo_financiar = htmlspecialchars($data['saldo_financiar'] ?? '$0');
$cuota_mensual = htmlspecialchars($data['cuota_mensual'] ?? '$0');

// Extraer datos del proyecto
$titulo_proyecto = htmlspecialchars($data['titulo_proyecto'] ?? 'Proyecto Inmobiliario');
$logo_url = filter_var($data['logo_url'] ?? '', FILTER_VALIDATE_URL) ? $data['logo_url'] : '';
$color_primario = htmlspecialchars($data['color_primario'] ?? '#f26522');
$fecha = htmlspecialchars($data['fecha'] ?? date("d/m/Y"));
$ref_text = htmlspecialchars($data['ref_text'] ?? time());

// Extraer datos del asesor
$asesor_nombre = htmlspecialchars($data['asesor_nombre'] ?? 'Asesor Comercial');
$asesor_tel = htmlspecialchars($data['asesor_tel'] ?? '573217399818');
$project_url = filter_var($data['project_url'] ?? '', FILTER_VALIDATE_URL) ? $data['project_url'] : '#';

// Formatear el teléfono para el enlace de WhatsApp (remover caracteres que no sean números)
$wa_tel = preg_replace('/[^0-9]/', '', $asesor_tel);

// Construir HTML del correo
$logo_html = $logo_url ? "<img src='{$logo_url}' style='max-height: 80px; max-width: 200px;' alt='Logo Proyecto'>" : "";

$wa_mensaje = urlencode("Hola {$asesor_nombre}, me gustaría ampliar la información sobre la cotización del {$lote_nombre} en {$titulo_proyecto}.");
$wa_link = "https://wa.me/{$wa_tel}?text={$wa_mensaje}";

$html_template = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<title>Bienvenido - {$titulo_proyecto}</title>
<style>
    body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; }
    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 20px; margin-bottom: 20px; }
    .header-bar { height: 8px; background: linear-gradient(90deg, {$color_primario}, #333333); width: 100%; }
    .header { padding: 40px 40px 20px 40px; text-align: center; border-bottom: 1px solid #eeeeee; }
    .content { padding: 40px; color: #333333; line-height: 1.6; }
    .title { font-size: 24px; font-weight: bold; color: #111111; letter-spacing: 1px; margin-bottom: 10px; }
    .subtitle { font-size: 16px; color: #555555; margin-top: 0; margin-bottom: 30px; }

    .lote-box { background-color: #f9f9f9; border: 1px solid #eeeeee; padding: 25px; border-radius: 8px; margin-bottom: 30px; text-align: center; }
    .lote-box-title { color: {$color_primario}; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; font-weight: bold; }
    .lote-box-name { font-size: 24px; font-weight: bold; margin: 0 0 10px 0; color: #111; }
    .lote-box-area { font-size: 16px; color: #666; }

    .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border: 1px solid #eeeeee; border-radius: 8px; overflow: hidden; }
    .table td { padding: 15px 20px; border-bottom: 1px solid #eeeeee; font-size: 15px; }
    .table tr:nth-child(even) { background-color: #fafafa; }
    .td-label { color: #555555; }
    .td-value { text-align: right; font-weight: bold; color: #111111; }

    .total-row { background-color: #111111 !important; }
    .total-row .td-label { color: #ffffff; font-size: 15px; text-transform: uppercase; }
    .total-row .td-value { color: {$color_primario}; font-size: 18px; }

    .next-steps { text-align: center; margin-bottom: 30px; padding: 20px; background-color: rgba(" . hexdec(substr($color_primario, 1, 2)) . ", " . hexdec(substr($color_primario, 3, 2)) . ", " . hexdec(substr($color_primario, 5, 2)) . ", 0.05); border-radius: 8px; border: 1px solid rgba(" . hexdec(substr($color_primario, 1, 2)) . ", " . hexdec(substr($color_primario, 3, 2)) . ", " . hexdec(substr($color_primario, 5, 2)) . ", 0.2); }
    .next-steps-title { font-size: 16px; font-weight: bold; color: {$color_primario}; margin-bottom: 10px; }

    .button-container { text-align: center; margin-top: 30px; display: block; }
    .btn { display: inline-block; padding: 14px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 15px; margin: 5px; text-transform: uppercase; letter-spacing: 1px; }
    .btn-primary { background-color: #25D366; color: #ffffff; }
    .btn-secondary { background-color: #111111; color: #ffffff; }

    .footer { padding: 30px 40px; text-align: center; background-color: #fcfcfc; border-top: 1px solid #eeeeee; font-size: 12px; color: #888888; }
</style>
</head>
<body>
<div style='background-color: #f4f4f4; padding: 40px 0;'>
    <div class='container'>
        <div class='header-bar'></div>
        <div class='header'>
            {$logo_html}
        </div>
        <div class='content'>
            <h1 class='title'>¡Bienvenido {$cliente_nombre} al proyecto {$titulo_proyecto}!</h1>
            <p class='subtitle'>Tu {$lote_nombre} te espera.</p>

            <p style='font-size: 16px; margin-top: 0;'>Nos emociona compartir contigo el resumen de tu inversión. Has dado el primer paso hacia una gran oportunidad en <strong>{$titulo_proyecto}</strong>.</p>

            <div class='lote-box'>
                <div class='lote-box-title'>Información del Lote</div>
                <h2 class='lote-box-name'>{$lote_nombre}</h2>
                <div class='lote-box-area'>Área Total: <strong>{$lote_area}</strong></div>
            </div>

            <h3 style='font-size: 15px; text-transform: uppercase; text-align: center; margin-bottom: 15px; letter-spacing: 1px; color: #333;'>Resumen de Inversión</h3>
            <table class='table'>
                <tr>
                    <td class='td-label'>Valor Total</td>
                    <td class='td-value'>{$valor_total}</td>
                </tr>
                <tr>
                    <td class='td-label'>Aporte Inicial Sugerido</td>
                    <td class='td-value'>{$cuota_inicial}</td>
                </tr>
                <tr>
                    <td class='td-label'>Saldo a Financiar</td>
                    <td class='td-value'>{$saldo_financiar}</td>
                </tr>
                <tr>
                    <td class='td-label'>Plazo</td>
                    <td class='td-value'>{$meses} Meses</td>
                </tr>
                <tr class='total-row'>
                    <td class='td-label'>Cuota Mensual Estimada</td>
                    <td class='td-value'>{$cuota_mensual}</td>
                </tr>
            </table>

            <div class='next-steps'>
                <div class='next-steps-title'>Paso a paso a seguir</div>
                <p style='margin: 0; color: #444; font-size: 15px;'>Un asesor te contactará muy pronto para ampliar la información, resolver tus dudas y acompañarte en el proceso.</p>
            </div>

            <div class='button-container'>
                <a href='{$wa_link}' class='btn btn-primary' style='color: #ffffff;'>Escribir por WhatsApp</a>
                <a href='{$project_url}' class='btn btn-secondary' style='color: #ffffff;'>Hacer otra cotización</a>
            </div>

        </div>
        <div class='footer'>
            <p>Referencia: {$ref_text} | Fecha: {$fecha}</p>
            <p>Este documento es de carácter informativo y no representa un compromiso legal ni una promesa de compraventa. Los valores, áreas y condiciones están sujetos a verificación y posibles modificaciones sin previo aviso.</p>
        </div>
    </div>
</div>
</body>
</html>
";

// Plantilla HTML para el correo del administrador (Notificación de nuevo lead)
$html_admin_template = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<title>Nuevo Lead - {$titulo_proyecto}</title>
<style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; border-top: 4px solid {$color_primario}; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    h2 { color: #111; margin-top: 0; }
    .info-group { margin-bottom: 20px; }
    .label { font-size: 12px; color: #777; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .value { font-size: 16px; color: #222; font-weight: bold; }
    .divider { border-top: 1px solid #eee; margin: 20px 0; }
</style>
</head>
<body>
    <div class='container'>
        <h2>Nueva Simulación de Inversión</h2>
        <p>Se ha generado una nueva cotización en el proyecto <strong>{$titulo_proyecto}</strong>.</p>

        <div class='divider'></div>

        <h3>Datos del Cliente</h3>
        <div class='info-group'><div class='label'>Nombre</div><div class='value'>{$cliente_nombre}</div></div>
        <div class='info-group'><div class='label'>Teléfono</div><div class='value'>{$cliente_tel}</div></div>
        <div class='info-group'><div class='label'>Email</div><div class='value'>{$cliente_email}</div></div>
        <div class='info-group'><div class='label'>Dirección</div><div class='value'>{$cliente_dir}</div></div>

        <div class='divider'></div>

        <h3>Detalles de la Simulación</h3>
        <div class='info-group'><div class='label'>Lote de Interés</div><div class='value'>{$lote_nombre} ({$lote_area})</div></div>
        <div class='info-group'><div class='label'>Valor Total</div><div class='value'>{$valor_total}</div></div>
        <div class='info-group'><div class='label'>Cuota Inicial Sugerida</div><div class='value'>{$cuota_inicial}</div></div>
        <div class='info-group'><div class='label'>Saldo a Financiar</div><div class='value'>{$saldo_financiar} a {$meses} meses</div></div>
        <div class='info-group'><div class='label'>Cuota Mensual Estimada</div><div class='value'>{$cuota_mensual}</div></div>

        <div class='divider'></div>
        <p style='font-size: 12px; color: #888;'>Notificación automática del sistema de cotizaciones de {$titulo_proyecto}.</p>
    </div>
</body>
</html>
";

// Cabeceras del correo para el cliente
$asunto_cliente = "¡Bienvenido a {$titulo_proyecto}! Tu cotización está lista";
$headers_cliente = "MIME-Version: 1.0\r\n";
$headers_cliente .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers_cliente .= "From: {$nombre_remitente} <{$remitente}>\r\n";
$headers_cliente .= "Reply-To: {$remitente}\r\n";

// Cabeceras del correo para el administrador
$admin_email = "sebastianestupinanb@gmail.com";
$asunto_admin = "Nuevo Lead: {$cliente_nombre} - {$titulo_proyecto}";
$headers_admin = "MIME-Version: 1.0\r\n";
$headers_admin .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers_admin .= "From: Notificaciones Plataforma <{$remitente}>\r\n";
$headers_admin .= "Reply-To: {$cliente_email}\r\n";


// Enviar el correo al cliente y al administrador
$mail_cliente = mail($cliente_email, $asunto_cliente, $html_template, $headers_cliente);
$mail_admin = mail($admin_email, $asunto_admin, $html_admin_template, $headers_admin);

if ($mail_cliente) {
    echo json_encode(["status" => "success", "message" => "Correos enviados correctamente."]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Hubo un error al intentar enviar el correo al cliente. Verifica la configuración de tu servidor."]);
}
?>
