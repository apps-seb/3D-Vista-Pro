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
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Detalles de tu Inversión - {$titulo_proyecto}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');
    body { margin: 0; padding: 0; font-family: 'Montserrat', Arial, sans-serif; background-color: #f5f7fa; color: #333333; -webkit-font-smoothing: antialiased; }
    .wrapper { background-color: #f5f7fa; padding: 40px 20px; }
    .container { max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

    .header-accent { height: 6px; background: linear-gradient(90deg, {$color_primario}, #111111); width: 100%; }
    .header { padding: 40px 40px 30px; text-align: center; border-bottom: 1px solid #f0f0f0; }
    .logo-container { margin-bottom: 10px; }

    .content { padding: 45px 40px; }

    .greeting { font-size: 22px; font-weight: 300; color: #111111; margin-top: 0; margin-bottom: 10px; text-align: center; }
    .greeting strong { font-weight: 700; }
    .intro-text { font-size: 15px; color: #555555; line-height: 1.6; text-align: center; margin-bottom: 35px; }

    .lote-highlight { background: linear-gradient(135deg, rgba(" . hexdec(substr($color_primario, 1, 2)) . ", " . hexdec(substr($color_primario, 3, 2)) . ", " . hexdec(substr($color_primario, 5, 2)) . ", 0.05), rgba(" . hexdec(substr($color_primario, 1, 2)) . ", " . hexdec(substr($color_primario, 3, 2)) . ", " . hexdec(substr($color_primario, 5, 2)) . ", 0.15)); border: 1px solid rgba(" . hexdec(substr($color_primario, 1, 2)) . ", " . hexdec(substr($color_primario, 3, 2)) . ", " . hexdec(substr($color_primario, 5, 2)) . ", 0.2); padding: 30px; border-radius: 12px; margin-bottom: 35px; text-align: center; position: relative; }
    .lote-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background-color: {$color_primario}; color: white; padding: 4px 15px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .lote-name { font-size: 28px; font-weight: 700; margin: 15px 0 5px; color: #111; }
    .lote-area { font-size: 15px; color: #555; font-weight: 400; }

    .section-title { font-size: 14px; text-transform: uppercase; letter-spacing: 1.5px; color: #888; text-align: center; margin-bottom: 20px; font-weight: 600; }

    .financial-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 35px; border: 1px solid #eaeaea; border-radius: 8px; overflow: hidden; }
    .financial-table td { padding: 18px 25px; border-bottom: 1px solid #eaeaea; font-size: 15px; }
    .financial-table tr:last-child td { border-bottom: none; }
    .financial-table tr:nth-child(odd) { background-color: #fafbfc; }
    .label-col { color: #666666; font-weight: 400; }
    .value-col { text-align: right; font-weight: 600; color: #111111; }

    .total-row td { background-color: #111111 !important; }
    .total-row .label-col { color: #ffffff; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
    .total-row .value-col { color: {$color_primario}; font-size: 20px; font-weight: 700; }

    .steps-container { background-color: #ffffff; border: 1px dashed #cccccc; padding: 25px; border-radius: 8px; margin-bottom: 35px; }
    .steps-title { font-size: 16px; font-weight: 700; color: #111; margin-top: 0; margin-bottom: 15px; text-align: center; }
    .step-item { display: table; width: 100%; margin-bottom: 12px; }
    .step-number { display: table-cell; width: 28px; height: 28px; background-color: {$color_primario}; color: white; text-align: center; border-radius: 50%; font-size: 14px; font-weight: bold; vertical-align: middle; }
    .step-text { display: table-cell; padding-left: 15px; font-size: 14px; color: #555; vertical-align: middle; line-height: 1.5; }

    .cta-section { text-align: center; padding-top: 10px; }
    .btn { display: inline-block; padding: 16px 32px; text-decoration: none; font-weight: 600; border-radius: 30px; font-size: 15px; margin: 10px 5px; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .btn-primary { background-color: #25D366; color: #ffffff; }
    .btn-secondary { background-color: #ffffff; color: #111111; border: 1px solid #111111; }

    .footer { padding: 35px 40px; text-align: center; background-color: #111111; color: #999999; font-size: 12px; border-radius: 0 0 12px 12px; }
    .footer p { margin: 8px 0; line-height: 1.5; }
    .ref-line { font-family: monospace; color: #777; margin-bottom: 15px !important; }

    @media only screen and (max-width: 600px) {
        .content { padding: 30px 20px; }
        .header { padding: 30px 20px 20px; }
        .financial-table td { padding: 15px; }
        .btn { display: block; margin: 10px 0; width: 100%; box-sizing: border-box; }
    }
</style>
</head>
<body>
<div class='wrapper'>
    <div class='container'>
        <div class='header-accent'></div>
        <div class='header'>
            <div class='logo-container'>
                {$logo_html}
            </div>
        </div>

        <div class='content'>
            <h1 class='greeting'>Hola, <strong>{$cliente_nombre}</strong></h1>
            <p class='intro-text'>Nos emociona compartir contigo el resumen de tu proyección de inversión. Has dado un paso excelente hacia tu futuro en <strong>{$titulo_proyecto}</strong>.</p>

            <div class='lote-highlight'>
                <div class='lote-badge'>Tu Selección</div>
                <div class='lote-name'>{$lote_nombre}</div>
                <div class='lote-area'>Área Total: <strong>{$lote_area}</strong></div>
            </div>

            <div class='section-title'>Proyección Financiera</div>

            <table class='financial-table'>
                <tr>
                    <td class='label-col'>Valor Total de Inversión</td>
                    <td class='value-col'>{$valor_total}</td>
                </tr>
                <tr>
                    <td class='label-col'>Aporte Inicial Sugerido</td>
                    <td class='value-col'>{$cuota_inicial}</td>
                </tr>
                <tr>
                    <td class='label-col'>Saldo a Financiar</td>
                    <td class='value-col'>{$saldo_financiar}</td>
                </tr>
                <tr>
                    <td class='label-col'>Plazo Seleccionado</td>
                    <td class='value-col'>{$meses} Meses</td>
                </tr>
                <tr class='total-row'>
                    <td class='label-col'>Cuota Mensual Estimada</td>
                    <td class='value-col'>{$cuota_mensual}</td>
                </tr>
            </table>

            <div class='steps-container'>
                <h3 class='steps-title'>¿Qué sigue ahora?</h3>

                <div class='step-item'>
                    <div style='display:table-cell; width:28px;'><div style='width:28px; height:28px; background-color:{$color_primario}; color:white; border-radius:50%; text-align:center; line-height:28px; font-weight:bold; font-size:14px;'>1</div></div>
                    <div class='step-text'>Revisa detenidamente esta propuesta financiera diseñada para ti.</div>
                </div>

                <div class='step-item'>
                    <div style='display:table-cell; width:28px;'><div style='width:28px; height:28px; background-color:{$color_primario}; color:white; border-radius:50%; text-align:center; line-height:28px; font-weight:bold; font-size:14px;'>2</div></div>
                    <div class='step-text'>Contacta a tu asesor asignado para agendar una visita o llamada.</div>
                </div>

                <div class='step-item'>
                    <div style='display:table-cell; width:28px;'><div style='width:28px; height:28px; background-color:{$color_primario}; color:white; border-radius:50%; text-align:center; line-height:28px; font-weight:bold; font-size:14px;'>3</div></div>
                    <div class='step-text'>Reserva tu lote y asegura las condiciones de esta cotización.</div>
                </div>
            </div>

            <div class='cta-section'>
                <a href='{$wa_link}' class='btn btn-primary' style='color: #ffffff;'>Contactar Asesor por WhatsApp</a>
                <a href='{$project_url}' class='btn btn-secondary' style='color: #111111;'>Regresar al Proyecto</a>
            </div>

        </div>

        <div class='footer'>
            <p class='ref-line'>Ref: {$ref_text} | Fecha: {$fecha}</p>
            <p>Este documento es de carácter informativo y no representa un compromiso legal ni una promesa de compraventa.</p>
            <p>Los valores, áreas y condiciones están sujetos a verificación y posibles modificaciones sin previo aviso por parte de la constructora.</p>
            <p style='margin-top: 15px; font-size: 10px;'>&copy; " . date('Y') . " {$titulo_proyecto}. Todos los derechos reservados.</p>
        </div>
    </div>
</div>
</body>
</html>
";

// Formatear el teléfono del cliente para enlaces
$cliente_wa_tel = preg_replace('/[^0-9]/', '', $cliente_tel);
// Si no empieza con código de país (ej. 57), podríamos agregarlo o asumir que el usuario lo incluye.
// Por seguridad, si tiene 10 dígitos (típico de Colombia), le agregamos el 57
if (strlen($cliente_wa_tel) == 10) {
    $cliente_wa_tel = "57" . $cliente_wa_tel;
}
$wa_admin_link = "https://wa.me/{$cliente_wa_tel}?text=" . urlencode("Hola {$cliente_nombre}, soy {$asesor_nombre} de {$titulo_proyecto}. Recibí tu solicitud de cotización por el {$lote_nombre} y me encantaría ayudarte.");

// Plantilla HTML para el correo del administrador (Notificación de nuevo lead)
$html_admin_template = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Nuevo Lead - {$titulo_proyecto}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; color: #333; -webkit-font-smoothing: antialiased; }
    .wrapper { padding: 40px 20px; }
    .crm-container { max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }

    .crm-header { background-color: #1a1f36; padding: 30px; display: flex; align-items: center; justify-content: space-between; }
    .crm-title { margin: 0; color: #ffffff; font-size: 20px; font-weight: 600; }
    .crm-badge { background-color: {$color_primario}; color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

    .crm-content { padding: 30px; }

    .crm-intro { font-size: 15px; color: #555; margin-bottom: 25px; line-height: 1.5; }
    .crm-intro strong { color: #111; }

    .data-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin-bottom: 25px; }
    .card-title { font-size: 14px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-top: 0; margin-bottom: 20px; letter-spacing: 0.5px; display: flex; align-items: center; }
    .card-title::before { content: ''; display: inline-block; width: 4px; height: 16px; background-color: {$color_primario}; margin-right: 10px; border-radius: 2px; }

    .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .data-item { margin-bottom: 5px; }
    .data-label { font-size: 12px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; font-weight: 600; }
    .data-value { font-size: 15px; color: #0f172a; font-weight: 500; word-break: break-word; }
    .data-value-highlight { font-size: 18px; color: {$color_primario}; font-weight: 700; }

    .actions-container { display: flex; gap: 15px; margin-top: 30px; justify-content: center; flex-wrap: wrap; }
    .action-btn { display: inline-block; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; text-align: center; transition: all 0.2s; }
    .btn-whatsapp { background-color: #25D366; color: white; border: 1px solid #20b958; }
    .btn-email { background-color: #3b82f6; color: white; border: 1px solid #2563eb; }
    .btn-call { background-color: #ffffff; color: #0f172a; border: 1px solid #cbd5e1; }

    .crm-footer { background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; }

    @media (max-width: 600px) {
        .data-grid { grid-template-columns: 1fr; gap: 15px; }
        .crm-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        .action-btn { width: 100%; box-sizing: border-box; }
    }
</style>
</head>
<body>
<div class='wrapper'>
    <div class='crm-container'>
        <div class='crm-header'>
            <h2 class='crm-title'>Nuevo Lead Recibido</h2>
            <div class='crm-badge'>ALTA INTENCIÓN</div>
        </div>

        <div class='crm-content'>
            <p class='crm-intro'>El sistema ha registrado una nueva simulación de inversión para el proyecto <strong>{$titulo_proyecto}</strong>.</p>

            <div class='data-card'>
                <h3 class='card-title'>Datos del Contacto</h3>
                <div class='data-grid'>
                    <div class='data-item'>
                        <div class='data-label'>Nombre Completo</div>
                        <div class='data-value'>{$cliente_nombre}</div>
                    </div>
                    <div class='data-item'>
                        <div class='data-label'>Teléfono</div>
                        <div class='data-value'>{$cliente_tel}</div>
                    </div>
                    <div class='data-item'>
                        <div class='data-label'>Correo Electrónico</div>
                        <div class='data-value'><a href='mailto:{$cliente_email}' style='color:#3b82f6; text-decoration:none;'>{$cliente_email}</a></div>
                    </div>
                    <div class='data-item'>
                        <div class='data-label'>Ubicación</div>
                        <div class='data-value'>{$cliente_dir}</div>
                    </div>
                </div>

                <div class='actions-container'>
                    <a href='{$wa_admin_link}' class='action-btn btn-whatsapp'>Contactar por WhatsApp</a>
                    <a href='tel:{$cliente_tel}' class='action-btn btn-call'>Llamar</a>
                    <a href='mailto:{$cliente_email}?subject=Información sobre {$lote_nombre} en {$titulo_proyecto}&body=Hola {$cliente_nombre},' class='action-btn btn-email'>Enviar Correo</a>
                </div>
            </div>

            <div class='data-card'>
                <h3 class='card-title'>Detalles de la Proyección</h3>
                <div class='data-grid'>
                    <div class='data-item'>
                        <div class='data-label'>Lote Seleccionado</div>
                        <div class='data-value' style='font-weight: 700;'>{$lote_nombre} ({$lote_area})</div>
                    </div>
                    <div class='data-item'>
                        <div class='data-label'>Valor Total</div>
                        <div class='data-value'>{$valor_total}</div>
                    </div>
                    <div class='data-item'>
                        <div class='data-label'>Cuota Inicial Sugerida</div>
                        <div class='data-value'>{$cuota_inicial}</div>
                    </div>
                    <div class='data-item'>
                        <div class='data-label'>Plan de Financiación</div>
                        <div class='data-value'>{$saldo_financiar} a {$meses} meses</div>
                    </div>
                    <div class='data-item' style='grid-column: 1 / -1; margin-top: 10px; background-color: white; padding: 15px; border-radius: 6px; border: 1px dashed #cbd5e1;'>
                        <div class='data-label'>Cuota Mensual Estimada</div>
                        <div class='data-value-highlight'>{$cuota_mensual}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class='crm-footer'>
            <p style='margin: 0;'>Notificación automática del CRM de Cotizaciones - <strong>{$titulo_proyecto}</strong></p>
            <p style='margin: 5px 0 0 0;'>Ref: {$ref_text} | Fecha: {$fecha}</p>
        </div>
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
