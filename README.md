# track24_integration
Интеграция с сервисом отслеживания почтовых отправлений
```php
<?$APPLICATION->IncludeComponent(
    "leadspace:track24.tracker",
    "",
    Array(
        "TRACK_CODE" => "LC166805240CN",
        "DOMAIN" => "",
        "SHOW_TITLE" => "Y",
        "TITLE" => "Отслеживание посылки",
        "SHOW_STATISTICS" => "Y",
        "AUTO_UPDATE" => "60", // обновление каждые 60 сек
        "CACHE_TIME" => "3600"
    )
);?>
