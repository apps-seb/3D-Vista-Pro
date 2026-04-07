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
// Permitir URLs normales o strings base64 (data:image/...)
$raw_logo_url = $data['logo_url'] ?? '';
$logo_url = '';
if (filter_var($raw_logo_url, FILTER_VALIDATE_URL) || strpos($raw_logo_url, 'data:image/') === 0) {
    $logo_url = $raw_logo_url;
}
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
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Bienvenido - {$titulo_proyecto}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');
    body { margin: 0; padding: 0; font-family: 'Montserrat', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8f9fa; -webkit-font-smoothing: antialiased; }
    .wrapper { width: 100%; table-layout: fixed; background-color: #f8f9fa; padding-bottom: 60px; }
    .webkit { max-width: 600px; margin: 0 auto; }
    .outer { margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: 'Montserrat', sans-serif; color: #333333; }

    .header-gradient { height: 6px; background: linear-gradient(90deg, {$color_primario}, #ff8c00); }
    .header { background-color: #ffffff; padding: 40px 30px; text-align: center; }

    .main-body { background-color: #ffffff; padding: 0 40px 40px 40px; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }

    .title { font-size: 26px; font-weight: 700; color: #111111; letter-spacing: -0.5px; margin-bottom: 8px; margin-top: 0; }
    .subtitle { font-size: 18px; color: {$color_primario}; font-weight: 600; margin-top: 0; margin-bottom: 35px; }

    .intro-text { font-size: 15px; line-height: 1.6; color: #555555; margin-bottom: 30px; }

    .card { background: linear-gradient(145deg, #ffffff, #fcfcfc); border: 1px solid #eeeeee; border-radius: 12px; padding: 30px; margin-bottom: 30px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .card-title { color: #888888; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; font-weight: 600; }
    .card-value-main { font-size: 28px; font-weight: 700; color: #111111; margin: 0 0 10px 0; }
    .card-value-sub { font-size: 16px; color: #666666; font-weight: 400; }

    .table-container { border: 1px solid #eeeeee; border-radius: 12px; overflow: hidden; margin-bottom: 35px; }
    .table { width: 100%; border-collapse: collapse; }
    .table td { padding: 18px 25px; border-bottom: 1px solid #eeeeee; font-size: 14px; }
    .table tr:last-child td { border-bottom: none; }
    .table tr:nth-child(even) { background-color: #fdfdfd; }
    .td-label { color: #666666; font-weight: 400; }
    .td-value { text-align: right; font-weight: 600; color: #222222; }

    .total-row { background-color: #111111 !important; }
    .total-row .td-label { color: #ffffff; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
    .total-row .td-value { color: {$color_primario}; font-size: 20px; font-weight: 700; }

    .next-steps { text-align: center; padding: 25px; background-color: rgba(" . hexdec(substr($color_primario, 1, 2)) . ", " . hexdec(substr($color_primario, 3, 2)) . ", " . hexdec(substr($color_primario, 5, 2)) . ", 0.05); border-radius: 12px; border-left: 4px solid {$color_primario}; margin-bottom: 35px; }
    .next-steps-title { font-size: 15px; font-weight: 700; color: #111111; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
    .next-steps-text { margin: 0; color: #555555; font-size: 14px; line-height: 1.5; }

    .button-container { text-align: center; margin-top: 10px; }
    .btn { display: inline-block; padding: 16px 28px; text-decoration: none; font-weight: 600; border-radius: 8px; font-size: 14px; margin: 8px; text-transform: uppercase; letter-spacing: 1px; transition: opacity 0.3s; }
    .btn-primary { background-color: #25D366; color: #ffffff !important; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); }
    .btn-secondary { background-color: #111111; color: #ffffff !important; box-shadow: 0 4px 15px rgba(17, 17, 17, 0.2); }

    .footer { padding: 40px 30px; text-align: center; color: #999999; font-size: 11px; line-height: 1.6; }
    .footer-logo { max-height: 40px; margin-bottom: 15px; opacity: 0.6; filter: grayscale(100%); }
    .divider { height: 1px; background-color: #dddddd; margin: 20px auto; width: 50%; }
</style>
</head>
<body>
<div class='wrapper'>
    <div class='webkit'>
        <table class='outer' align='center'>
            <tr>
                <td>
                    <div class='header-gradient'></div>
                    <div class='header'>
                        {$logo_html}
                    </div>
                    <div class='main-body'>
                        <h1 class='title'>¡Bienvenido, {$cliente_nombre}!</h1>
                        <p class='subtitle'>Tu {$lote_nombre} te espera en {$titulo_proyecto}</p>

                        <p class='intro-text'>Nos emociona compartir contigo el resumen de tu inversión. Has dado el primer paso hacia una gran oportunidad y estamos aquí para acompañarte en todo el proceso.</p>

                        <div class='card'>
                            <div class='card-title'>Información del Inmueble</div>
                            <h2 class='card-value-main'>{$lote_nombre}</h2>
                            <div class='card-value-sub'>Área Total: <strong>{$lote_area}</strong></div>
                        </div>

                        <div class='card-title' style='text-align: center; margin-bottom: 15px;'>Resumen Financiero</div>
                        <div class='table-container'>
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
                                    <td class='td-label'>Plazo Estimado</td>
                                    <td class='td-value'>{$meses} Meses</td>
                                </tr>
                                <tr class='total-row'>
                                    <td class='td-label'>Inversión Mensual</td>
                                    <td class='td-value'>{$cuota_mensual}</td>
                                </tr>
                            </table>
                        </div>

                        <div class='next-steps'>
                            <div class='next-steps-title'>Paso a paso a seguir</div>
                            <p class='next-steps-text'>Un asesor especializado de nuestro equipo se pondrá en contacto contigo muy pronto para ampliar la información, resolver tus dudas y guiarte.</p>
                        </div>

                        <div class='button-container'>
                            <a href='{$wa_link}' class='btn btn-primary'>Escribir por WhatsApp</a>
                            <a href='{$project_url}' class='btn btn-secondary'>Hacer otra cotización</a>
                        </div>
                    </div>

                    <div class='footer'>
                        " . ($logo_url ? "<img src='{$logo_url}' class='footer-logo' alt='Logo'>" : "") . "
                        <p style='margin: 0;'><strong>Referencia de cotización:</strong> {$ref_text} &nbsp;|&nbsp; <strong>Fecha:</strong> {$fecha}</p>
                        <div class='divider'></div>
                        <p style='margin: 0;'>Este documento es de carácter informativo y no representa un compromiso legal ni una promesa de compraventa. Los valores, áreas y condiciones están sujetos a verificación y posibles modificaciones sin previo aviso.</p>
                        <p style='margin: 15px 0 0 0; color: #bbbbbb;'>&copy; " . date('Y') . " {$titulo_proyecto}. Todos los derechos reservados.</p>
                    </div>
                </td>
            </tr>
        </table>
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
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Nuevo Lead - {$titulo_proyecto}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
    body { font-family: 'Montserrat', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 40px 20px; -webkit-font-smoothing: antialiased; }
    .wrapper { width: 100%; table-layout: fixed; }
    .webkit { max-width: 600px; margin: 0 auto; }
    .outer { margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; background-color: #ffffff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }

    .header { background: linear-gradient(135deg, #111111, #2a2a2a); padding: 30px; text-align: center; border-bottom: 4px solid {$color_primario}; }
    .header h2 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
    .header p { color: #aaaaaa; margin: 5px 0 0 0; font-size: 14px; }

    .content { padding: 30px; }

    .section-title { font-size: 14px; color: {$color_primario}; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 2px solid #eeeeee; padding-bottom: 8px; margin-bottom: 20px; margin-top: 0; }

    .info-grid { display: block; margin-bottom: 30px; }
    .info-row { display: block; margin-bottom: 15px; }
    .label { font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; font-weight: 600; }
    .value { font-size: 16px; color: #111111; font-weight: 600; background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; }

    .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #888888; border-top: 1px solid #eeeeee; }
</style>
</head>
<body>
<div class='wrapper'>
    <div class='webkit'>
        <table class='outer' align='center'>
            <tr>
                <td>
                    <div class='header'>
                        <h2>Nuevo Lead Capturado</h2>
                        <p>{$titulo_proyecto}</p>
                    </div>

                    <div class='content'>
                        <h3 class='section-title'>Datos de Contacto</h3>
                        <div class='info-grid'>
                            <div class='info-row'>
                                <div class='label'>Nombre del Cliente</div>
                                <div class='value'>{$cliente_nombre}</div>
                            </div>
                            <div class='info-row'>
                                <div class='label'>Teléfono / Celular</div>
                                <div class='value'>{$cliente_tel}</div>
                            </div>
                            <div class='info-row'>
                                <div class='label'>Correo Electrónico</div>
                                <div class='value'>{$cliente_email}</div>
                            </div>
                            <div class='info-row'>
                                <div class='label'>Dirección Registrada</div>
                                <div class='value'>{$cliente_dir}</div>
                            </div>
                        </div>

                        <h3 class='section-title'>Detalles de Inversión</h3>
                        <div class='info-grid'>
                            <div class='info-row'>
                                <div class='label'>Inmueble / Lote de Interés</div>
                                <div class='value'>{$lote_nombre} ({$lote_area})</div>
                            </div>
                            <div class='info-row'>
                                <div class='label'>Valor Total</div>
                                <div class='value' style='color: {$color_primario};'>{$valor_total}</div>
                            </div>
                            <div class='info-row'>
                                <div class='label'>Plan de Pagos (Cuota Inicial / Saldo / Plazo)</div>
                                <div class='value'>{$cuota_inicial} / {$saldo_financiar} ({$meses} meses)</div>
                            </div>
                            <div class='info-row'>
                                <div class='label'>Proyección Cuota Mensual</div>
                                <div class='value'>{$cuota_mensual}</div>
                            </div>
                        </div>
                    </div>

                    <div class='footer'>
                        Notificación automática del CRM de <strong>{$titulo_proyecto}</strong>.<br>
                        Fecha de simulación: {$fecha}
                    </div>
                </td>
            </tr>
        </table>
    </div>
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
$headers_admin .= "From: {$nombre_remitente} <{$remitente}>\r\n";
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
