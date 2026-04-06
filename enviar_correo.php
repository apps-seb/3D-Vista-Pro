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
$remitente = "contacto@tudominio.com"; // <-- CAMBIAR POR TU CORREO DE HOSTINGER
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
$asesor_tel = htmlspecialchars($data['asesor_tel'] ?? 'N/A');


// Construir HTML del correo
$logo_html = $logo_url ? "<img src='{$logo_url}' style='max-height: 80px; max-width: 200px;' alt='Logo Proyecto'>" : "";

$html_template = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<title>Cotización - {$titulo_proyecto}</title>
<style>
    body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; }
    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 0; }
    .header-bar { height: 8px; background: linear-gradient(90deg, {$color_primario}, #333333); width: 100%; }
    .header { padding: 40px 40px 20px 40px; text-align: center; border-bottom: 1px solid #eeeeee; }
    .content { padding: 40px; color: #333333; line-height: 1.6; }
    .title { font-size: 22px; font-weight: bold; color: #111111; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
    .subtitle { font-size: 14px; color: #777777; margin-top: 0; margin-bottom: 30px; }

    .lote-box { background-color: #111111; color: #ffffff; padding: 25px; border-radius: 8px; margin-bottom: 30px; text-align: center; }
    .lote-box-title { color: {$color_primario}; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; }
    .lote-box-name { font-size: 26px; font-weight: bold; margin: 0 0 10px 0; }
    .lote-box-area { font-size: 18px; color: #dddddd; }

    .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border: 1px solid #eeeeee; border-radius: 8px; overflow: hidden; }
    .table td { padding: 15px 20px; border-bottom: 1px solid #eeeeee; font-size: 14px; }
    .table tr:nth-child(even) { background-color: #fafafa; }
    .td-label { color: #555555; }
    .td-value { text-align: right; font-weight: bold; color: #111111; }

    .total-row { background-color: #111111 !important; }
    .total-row .td-label { color: #ffffff; font-size: 14px; text-transform: uppercase; }
    .total-row .td-value { color: {$color_primario}; font-size: 18px; }

    .info-grid { display: table; width: 100%; margin-bottom: 30px; }
    .info-col { display: table-cell; width: 50%; padding-right: 20px; }
    .info-title { font-size: 12px; color: {$color_primario}; text-transform: uppercase; border-bottom: 1px solid {$color_primario}; padding-bottom: 5px; margin-bottom: 10px; }
    .info-text { font-size: 13px; margin: 0 0 5px 0; color: #555555; }

    .footer { padding: 30px 40px; text-align: center; background-color: #fcfcfc; border-top: 1px solid #eeeeee; font-size: 11px; color: #888888; }
</style>
</head>
<body>
<div style='background-color: #f4f4f4; padding: 40px 0;'>
    <div class='container'>
        <div class='header-bar'></div>
        <div class='header'>
            {$logo_html}
            <h1 class='title'>{$titulo_proyecto}</h1>
            <p class='subtitle'>Propuesta Comercial de Inversión</p>
        </div>
        <div class='content'>
            <p style='font-size: 16px; margin-top: 0;'>Hola <strong>{$cliente_nombre}</strong>,</p>
            <p>A continuación, te presentamos el resumen de tu simulación de financiamiento para tu inversión en <strong>{$titulo_proyecto}</strong>.</p>

            <div class='lote-box'>
                <div class='lote-box-title'>Especificación del Inmueble</div>
                <h2 class='lote-box-name'>{$lote_nombre}</h2>
                <div class='lote-box-area'>Área Total: {$lote_area}</div>
            </div>

            <h3 style='font-size: 14px; text-transform: uppercase; text-align: center; margin-bottom: 15px; letter-spacing: 1px;'>Proyección Financiera</h3>
            <table class='table'>
                <tr>
                    <td class='td-label'>Valor Total del Inmueble</td>
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
                    <td class='td-label'>Plazo de Financiación</td>
                    <td class='td-value'>{$meses} Meses</td>
                </tr>
                <tr class='total-row'>
                    <td class='td-label'>Inversión Mensual Estimada</td>
                    <td class='td-value'>{$cuota_mensual}</td>
                </tr>
            </table>

            <div class='info-grid'>
                <div class='info-col'>
                    <div class='info-title'>Datos del Inversor</div>
                    <p class='info-text'><strong>Nombre:</strong> {$cliente_nombre}</p>
                    <p class='info-text'><strong>Teléfono:</strong> {$cliente_tel}</p>
                    <p class='info-text'><strong>Email:</strong> {$cliente_email}</p>
                </div>
                <div class='info-col'>
                    <div class='info-title'>Asesor Comercial</div>
                    <p class='info-text'><strong>Nombre:</strong> {$asesor_nombre}</p>
                    <p class='info-text'><strong>Teléfono:</strong> {$asesor_tel}</p>
                </div>
            </div>

        </div>
        <div class='footer'>
            <p>Referencia de Cotización: {$ref_text} | Fecha: {$fecha}</p>
            <p>Este documento es de carácter informativo y no representa un compromiso legal ni una promesa de compraventa. Los valores, áreas y condiciones están sujetos a verificación y posibles modificaciones sin previo aviso.</p>
        </div>
    </div>
</div>
</body>
</html>
";

// Cabeceras del correo
$asunto = "Cotización de Inversión - {$titulo_proyecto}";
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: {$nombre_remitente} <{$remitente}>\r\n";
$headers .= "Reply-To: {$remitente}\r\n";

// Enviar el correo
if (mail($cliente_email, $asunto, $html_template, $headers)) {
    echo json_encode(["status" => "success", "message" => "Correo enviado correctamente."]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Hubo un error al intentar enviar el correo. Verifica la configuración de tu servidor."]);
}
?>
