<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = [
    "PARAMETERS" => [
        "TRACK_CODE" => [
            "PARENT" => "BASE",
            "NAME" => "Трек-код для отслеживания",
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ],
        "DOMAIN" => [
            "PARENT" => "BASE",
            "NAME" => "Домен (для API)",
            "TYPE" => "STRING",
            "DEFAULT" => "tk-gocargo.ru",
        ],
        "SHOW_TITLE" => [
            "PARENT" => "BASE",
            "NAME" => "Показывать заголовок",
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "TITLE" => [
            "PARENT" => "BASE",
            "NAME" => "Заголовок секции",
            "TYPE" => "STRING",
            "DEFAULT" => "Отслеживание посылки",
        ],
        "SHOW_STATISTICS" => [
            "PARENT" => "ADDITIONAL",
            "NAME" => "Показывать статистику доставки",
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "AUTO_UPDATE" => [
            "PARENT" => "ADDITIONAL",
            "NAME" => "Автообновление (секунд, 0 - отключено)",
            "TYPE" => "STRING",
            "DEFAULT" => "0",
        ],
        "CACHE_TIME" => [
            "DEFAULT" => 3600,
        ],
    ],
    "GROUPS" => [
        "ADDITIONAL" => [
            "NAME" => "Дополнительно"
        ],
    ]
];
?>