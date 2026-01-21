<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$module_id = 'leadspace.integrationtrack24';

if (!Loader::includeModule($module_id)) {
    ShowError('Модуль не установлен');
    return;
}

$request = Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$aTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => 'Настройки API',
        'TITLE' => 'Настройки подключения к Track24',
        'OPTIONS' => [
            [
                'api_key',
                'API ключ Track24',
                '',
                [
                    'text',
                    50,
                ],
            ],
        ],
    ],
];

if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) {
                continue;
            }

            $optionName = $arOption[0];
            $optionValue = $request->getPost($optionName);
            
            Option::set($module_id, $optionName, $optionValue);
        }
    }
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();

?>

<form method="post" name="track24_settings" action="<?= $APPLICATION->GetCurPage(); ?>?mid=<?= htmlspecialcharsbx($request['mid']); ?>&lang=<?= $request['lang']; ?>">
    <?= bitrix_sessid_post(); ?>
    
    <?php
    foreach ($aTabs as $aTab) {
        if ($aTab['OPTIONS']) {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
        }
    }
    ?>
    
    <div style="margin: 20px 0; padding: 10px; background-color: #f5f5f5; border-radius: 4px;">
        <strong>Поддержка:</strong> Если у вас возникли проблемы с настройкой API ключа, обратитесь в службу поддержки Track24.
    </div>
    
    <input type="submit" name="Update" value="Сохранить" class="adm-btn-save">
</form>

<?php
$tabControl->End();
?>