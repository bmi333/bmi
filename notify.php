<?php
date_default_timezone_set('America/Bogota');

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN
|--------------------------------------------------------------------------
*/

$discordWebhook = "https://discord.com/api/webhooks/1498450249373585418/nsKu-4388Jq4DBtivG2DLBNqMM7j_7rBklAw_ri63rhDV2JSL2OTLOUbEdwnXRjCrnGd";
$ipinfoToken = "9b5e5e56ed8eab";

/*
|--------------------------------------------------------------------------
| DETECTAR DATOS DEL VISITANTE
|--------------------------------------------------------------------------
*/

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] 
    ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
    ?? $_SERVER['REMOTE_ADDR'] 
    ?? 'No detectada';

if (strpos($ip, ',') !== false) {
    $ip = trim(explode(',', $ip)[0]);
}

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$referer = $_SERVER['HTTP_REFERER'] ?? 'Entrada directa';
$page = $_POST['page'] ?? 'index';
$time = date('Y-m-d H:i:s');

/*
|--------------------------------------------------------------------------
| CONSULTAR IPINFO
|--------------------------------------------------------------------------
*/

$geoUrl = "https://ipinfo.io/{$ip}/json?token={$ipinfoToken}";

$geoJson = @file_get_contents($geoUrl);
$geo = json_decode($geoJson, true);

$city = $geo['city'] ?? 'No detectada';
$region = $geo['region'] ?? 'No detectada';
$country = $geo['country'] ?? 'No detectado';
$org = $geo['org'] ?? 'No detectado';
$hostname = $geo['hostname'] ?? 'No detectado';
$postal = $geo['postal'] ?? 'No detectado';
$timezone = $geo['timezone'] ?? 'No detectada';

/*
|--------------------------------------------------------------------------
| MENSAJE PARA DISCORD
|--------------------------------------------------------------------------
*/

$message = [
    "username" => "Monitor Web",
    "avatar_url" => "https://cdn-icons-png.flaticon.com/512/2920/2920349.png",
    "embeds" => [[
        "title" => "🚨 Nuevo ingreso al index",
        "color" => 3066993,
        "fields" => [
            [
                "name" => "🌐 Página",
                "value" => $page,
                "inline" => false
            ],
            [
                "name" => "📍 IP",
                "value" => $ip,
                "inline" => true
            ],
            [
                "name" => "📡 Operador / Antena aproximada",
                "value" => $org,
                "inline" => true
            ],
            [
                "name" => "🏙️ Ciudad",
                "value" => $city,
                "inline" => true
            ],
            [
                "name" => "🗺️ Región",
                "value" => $region,
                "inline" => true
            ],
            [
                "name" => "🌎 País",
                "value" => $country,
                "inline" => true
            ],
            [
                "name" => "📮 Código postal",
                "value" => $postal,
                "inline" => true
            ],
            [
                "name" => "🕒 Zona horaria",
                "value" => $timezone,
                "inline" => true
            ],
            [
                "name" => "🔗 Referencia",
                "value" => $referer,
                "inline" => false
            ],
            [
                "name" => "📱 Dispositivo / Navegador",
                "value" => substr($userAgent, 0, 900),
                "inline" => false
            ],
            [
                "name" => "🖥️ Hostname",
                "value" => $hostname,
                "inline" => false
            ],
            [
                "name" => "🇨🇴 Hora Colombia",
                "value" => $time,
                "inline" => true
            ]
        ],
        "footer" => [
            "text" => "Notificación automática desde tu página web"
        ]
    ]]
];

/*
|--------------------------------------------------------------------------
| ENVIAR A DISCORD
|--------------------------------------------------------------------------
*/

$ch = curl_init($discordWebhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$error = curl_error($ch);

curl_close($ch);

http_response_code(204);
exit;
?>
