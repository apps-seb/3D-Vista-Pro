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

// Se asegura de usar estilos inline en la medida de lo posible para máxima compatibilidad con clientes de correo.
$html_template = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Bienvenido a {$titulo_proyecto}</title>
</head>
<body style=\"margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; -webkit-font-smoothing: antialiased;\">

<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"background-color: #f3f4f6; padding: 40px 0;\">
    <tr>
        <td align=\"center\">

            <!-- Contenedor Principal -->
            <table width=\"100%\" max-width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin: 0 auto;\">

                <!-- Barra superior de color -->
                <tr>
                    <td style=\"height: 8px; background-color: {$color_primario}; background: linear-gradient(90deg, {$color_primario}, #333333);\"></td>
                </tr>

                <!-- Encabezado con Logo -->
                <tr>
                    <td align=\"center\" style=\"padding: 40px 30px 20px 30px; background-color: #ffffff;\">
                        {$logo_html}
                    </td>
                </tr>

                <!-- Título y Bienvenida -->
                <tr>
                    <td align=\"center\" style=\"padding: 0 40px 30px 40px;\">
                        <h1 style=\"margin: 0; font-size: 28px; color: #111111; font-weight: bold; letter-spacing: -0.5px;\">¡Bienvenido, {$cliente_nombre}!</h1>
                        <p style=\"margin: 10px 0 0 0; font-size: 18px; color: {$color_primario}; font-weight: bold;\">Tu {$lote_nombre} te espera.</p>
                        <p style=\"margin: 20px 0 0 0; font-size: 16px; color: #555555; line-height: 1.6;\">Nos emociona compartir contigo el detalle de tu inversión en <strong>{$titulo_proyecto}</strong>. Has dado un gran paso y estamos aquí para acompañarte.</p>
                    </td>
                </tr>

                <!-- Ficha del Inmueble -->
                <tr>
                    <td align=\"center\" style=\"padding: 0 40px;\">
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;\">
                            <tr>
                                <td align=\"center\" style=\"padding: 25px;\">
                                    <p style=\"margin: 0 0 5px 0; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Información del Inmueble</p>
                                    <h2 style=\"margin: 0 0 5px 0; font-size: 26px; color: #111111;\">{$lote_nombre}</h2>
                                    <p style=\"margin: 0; font-size: 16px; color: #4b5563;\">Área Total: <strong>{$lote_area}</strong></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Tabla de Inversión -->
                <tr>
                    <td style=\"padding: 30px 40px;\">
                        <h3 style=\"margin: 0 0 15px 0; font-size: 15px; color: #111111; text-transform: uppercase; letter-spacing: 1px; text-align: center;\">Resumen de Inversión</h3>
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;\">
                            <tr>
                                <td style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 15px;\">Valor Total</td>
                                <td align=\"right\" style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #111111; font-weight: bold; font-size: 15px;\">{$valor_total}</td>
                            </tr>
                            <tr style=\"background-color: #f9fafb;\">
                                <td style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 15px;\">Aporte Inicial Sugerido</td>
                                <td align=\"right\" style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #111111; font-weight: bold; font-size: 15px;\">{$cuota_inicial}</td>
                            </tr>
                            <tr>
                                <td style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 15px;\">Saldo a Financiar</td>
                                <td align=\"right\" style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #111111; font-weight: bold; font-size: 15px;\">{$saldo_financiar}</td>
                            </tr>
                            <tr style=\"background-color: #f9fafb;\">
                                <td style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 15px;\">Plazo de Financiación</td>
                                <td align=\"right\" style=\"padding: 16px 20px; border-bottom: 1px solid #e5e7eb; color: #111111; font-weight: bold; font-size: 15px;\">{$meses} Meses</td>
                            </tr>
                            <tr style=\"background-color: #111111;\">
                                <td style=\"padding: 20px; color: #ffffff; font-size: 15px; font-weight: bold; text-transform: uppercase;\">Cuota Mensual Estimada</td>
                                <td align=\"right\" style=\"padding: 20px; color: {$color_primario}; font-weight: bold; font-size: 18px;\">{$cuota_mensual}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Siguientes Pasos -->
                <tr>
                    <td align=\"center\" style=\"padding: 0 40px 30px 40px;\">
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"background-color: #f9fafb; border-left: 4px solid {$color_primario}; padding: 20px;\">
                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 5px 0; font-size: 16px; color: #111111; font-weight: bold;\">Próximos pasos a seguir</p>
                                    <p style=\"margin: 0; font-size: 14px; color: #4b5563; line-height: 1.5;\">Un asesor especializado se pondrá en contacto contigo muy pronto para brindarte más información, resolver tus dudas y acompañarte en tu proceso de inversión.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Botones de Acción -->
                <tr>
                    <td align=\"center\" style=\"padding: 0 40px 40px 40px;\">
                        <!-- Botón WhatsApp -->
                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-bottom: 15px; width: 100%; max-width: 300px;\">
                            <tr>
                                <td align=\"center\" style=\"border-radius: 6px; background-color: #25D366;\">
                                    <a href=\"{$wa_link}\" target=\"_blank\" style=\"display: inline-block; padding: 15px 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: bold; text-transform: uppercase; width: 100%; box-sizing: border-box;\">Escribir por WhatsApp</a>
                                </td>
                            </tr>
                        </table>
                        <!-- Botón Cotizar -->
                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width: 100%; max-width: 300px;\">
                            <tr>
                                <td align=\"center\" style=\"border-radius: 6px; background-color: #111111;\">
                                    <a href=\"{$project_url}\" target=\"_blank\" style=\"display: inline-block; padding: 15px 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: bold; text-transform: uppercase; width: 100%; box-sizing: border-box;\">Hacer otra cotización</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align=\"center\" style=\"padding: 30px 40px; background-color: #f9fafb; border-top: 1px solid #e5e7eb;\">
                        <p style=\"margin: 0 0 10px 0; font-size: 12px; color: #6b7280;\"><strong>Referencia:</strong> {$ref_text} &nbsp;|&nbsp; <strong>Fecha:</strong> {$fecha}</p>
                        <p style=\"margin: 0; font-size: 11px; color: #9ca3af; line-height: 1.5;\">Este documento es de carácter informativo y no representa un compromiso legal ni una promesa de compraventa. Los valores, áreas y condiciones están sujetos a verificación y posibles modificaciones sin previo aviso.</p>
                        <p style=\"margin: 15px 0 0 0; font-size: 11px; color: #d1d5db;\">&copy; " . date('Y') . " {$titulo_proyecto}. Todos los derechos reservados.</p>
                    </td>
                </tr>

            </table>
            <!-- Fin Contenedor Principal -->

        </td>
    </tr>
</table>

</body>
</html>
";

// Plantilla HTML para el correo del administrador (Notificación de nuevo lead) usando diseño tabular robusto en línea
$html_admin_template = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Nuevo Lead - {$titulo_proyecto}</title>
</head>
<body style=\"margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f0f2f5; -webkit-font-smoothing: antialiased;\">

<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"background-color: #f0f2f5; padding: 40px 0;\">
    <tr>
        <td align=\"center\">

            <table width=\"100%\" max-width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin: 0 auto;\">

                <!-- Encabezado oscuro -->
                <tr>
                    <td align=\"center\" style=\"background-color: #111111; padding: 30px; border-bottom: 4px solid {$color_primario};\">
                        <h2 style=\"margin: 0; color: #ffffff; font-size: 22px; letter-spacing: 1px; text-transform: uppercase;\">Nuevo Lead Capturado</h2>
                        <p style=\"margin: 5px 0 0 0; color: #aaaaaa; font-size: 14px;\">{$titulo_proyecto}</p>
                    </td>
                </tr>

                <!-- Contenido -->
                <tr>
                    <td style=\"padding: 30px;\">

                        <!-- Sección Datos de Contacto -->
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                            <tr>
                                <td style=\"padding-bottom: 10px; border-bottom: 2px solid #eeeeee; margin-bottom: 20px;\">
                                    <h3 style=\"margin: 0; font-size: 14px; color: {$color_primario}; text-transform: uppercase; letter-spacing: 1.5px;\">Datos de Contacto</h3>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>

                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Nombre del Cliente</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: #111111; font-weight: bold;\">{$cliente_nombre}</div>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>
                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Teléfono / Celular</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: #111111; font-weight: bold;\">{$cliente_tel}</div>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>
                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Correo Electrónico</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: #111111; font-weight: bold;\">{$cliente_email}</div>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>
                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Dirección Registrada</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: #111111; font-weight: bold;\">{$cliente_dir}</div>
                                </td>
                            </tr>
                        </table>

                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                            <tr><td style=\"height: 30px;\"></td></tr>
                            <tr>
                                <td style=\"padding-bottom: 10px; border-bottom: 2px solid #eeeeee; margin-bottom: 20px;\">
                                    <h3 style=\"margin: 0; font-size: 14px; color: {$color_primario}; text-transform: uppercase; letter-spacing: 1.5px;\">Detalles de Inversión</h3>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>

                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Inmueble / Lote de Interés</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: #111111; font-weight: bold;\">{$lote_nombre} ({$lote_area})</div>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>
                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Valor Total</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: {$color_primario}; font-weight: bold;\">{$valor_total}</div>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>
                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Plan de Pagos (Cuota Inicial / Saldo / Plazo)</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: #111111; font-weight: bold;\">{$cuota_inicial} / {$saldo_financiar} ({$meses} meses)</div>
                                </td>
                            </tr>
                            <tr><td style=\"height: 15px;\"></td></tr>
                            <tr>
                                <td>
                                    <p style=\"margin: 0 0 4px 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;\">Proyección Cuota Mensual</p>
                                    <div style=\"background-color: #f8f9fa; padding: 12px 15px; border-radius: 6px; border: 1px solid #eeeeee; font-size: 16px; color: #111111; font-weight: bold;\">{$cuota_mensual}</div>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align=\"center\" style=\"background-color: #f8f9fa; padding: 20px; font-size: 12px; color: #888888; border-top: 1px solid #eeeeee;\">
                        Notificación automática del sistema de cotizaciones de <strong>{$titulo_proyecto}</strong>.<br>
                        Fecha de simulación: {$fecha}
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

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
