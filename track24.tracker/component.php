<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// Подключаем модули
CModule::IncludeModule("iblock");

// Обработка параметров
$arParams['TRACK_CODE'] = trim($arParams['TRACK_CODE']);
$arParams['DOMAIN'] = trim($arParams['DOMAIN']) ?: 'tk-gocargo.ru';
$arParams['SHOW_TITLE'] = $arParams['SHOW_TITLE'] !== 'N' ? 'Y' : 'N';
$arParams['TITLE'] = trim($arParams['TITLE']) ?: 'Отслеживание посылки';
$arParams['SHOW_STATISTICS'] = $arParams['SHOW_STATISTICS'] !== 'N' ? 'Y' : 'N';
$arParams['AUTO_UPDATE'] = intval($arParams['AUTO_UPDATE']);

// Передаем параметры в результат
$arResult['TRACK_CODE'] = $arParams['TRACK_CODE'];
$arResult['DOMAIN'] = $arParams['DOMAIN'];
$arResult['SHOW_TITLE'] = $arParams['SHOW_TITLE'];
$arResult['TITLE'] = $arParams['TITLE'];
$arResult['SHOW_STATISTICS'] = $arParams['SHOW_STATISTICS'];
$arResult['AUTO_UPDATE'] = $arParams['AUTO_UPDATE'];

// Инициализация переменных
$arResult['ERROR'] = '';
$arResult['DATA'] = null;

// Если трек-код не указан, показываем форму ввода
if (empty($arParams['TRACK_CODE'])) {
    $this->IncludeComponentTemplate();
    return;
}

// Формируем URL API
$apiUrl = sprintf(
    'https://api.track24.ru/tracking.json.php?apiKey=&code=%s&domain=%s',
    urlencode($arParams['TRACK_CODE']),
    urlencode($arParams['DOMAIN'])
);

// Получаем данные из API
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception('Ошибка при запросе к API: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception('API вернул код ошибки: ' . $httpCode);
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Ошибка при разборе JSON: ' . json_last_error_msg());
    }
    
    if (!isset($data['status']) || $data['status'] !== 'ok') {
        $errorMsg = 'API вернул ошибку в ответе';
        if (isset($data['error'])) {
            $errorMsg .= ': ' . $data['error'];
        }
        if (isset($data['message'])) {
            $errorMsg .= ' - ' . $data['message'];
        }
        throw new Exception($errorMsg);
    }
    
    $arResult['DATA'] = $data['data'];
    $arResult['SERVICES'] = $data['services'] ?? [];
    $arResult['DELIVERY_STAT'] = $data['deliveredStat'] ?? null;
    $arResult['RAW_RESPONSE'] = $data; // Для отладки
    
} catch (Exception $e) {
    $arResult['ERROR'] = $e->getMessage();
    $arResult['ERROR_DETAILS'] = [
        'http_code' => $httpCode ?? null,
        'raw_response' => $response ?? null
    ];
}

// Подключаем шаблон
$this->IncludeComponentTemplate();
?>