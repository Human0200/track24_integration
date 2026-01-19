<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);
?>

<!-- Track24 Tracker :: Start -->
<section class="track24-tracker">
    <div class="container-fluid">
        
        <?php if ($arResult['SHOW_TITLE'] === 'Y'): ?>
            <div class="track24-tracker__header">
                <h2 class="track24-tracker__title"><?= htmlspecialchars($arResult['TITLE']) ?></h2>
            </div>
        <?php endif; ?>

        <?php if (empty($arResult['TRACK_CODE'])): ?>
            <!-- Форма ввода трек-кода -->
            <div class="track24-tracker__form">
                <form method="get" action="">
                    <div class="form-group">
                        <label for="track-code-input">Введите трек-номер:</label>
                        <input type="text" 
                               id="track-code-input" 
                               name="track_code" 
                               class="form-control" 
                               placeholder="Например: LC166805240CN"
                               required>
                    </div>
                    <button type="submit" class="ui-btn ui-btn--gradient">Отследить посылку</button>
                </form>
            </div>
        <?php else: ?>
            
            <?php if ($arResult['ERROR']): ?>
                <!-- Ошибка -->
                <div class="alert alert-danger">
                    <strong>Ошибка:</strong> <?= htmlspecialchars($arResult['ERROR']) ?>
                </div>
            <?php elseif ($arResult['DATA']): ?>
                
                <!-- Основная информация -->
                <div class="track24-tracker__info">
                    <table class="table table-bordered">
                        <tr>
                            <th>Трек-номер</th>
                            <td><strong><?= htmlspecialchars($arResult['DATA']['trackCode']) ?></strong></td>
                        </tr>
                        <?php if ($arResult['DATA']['fromCountry']): ?>
                        <tr>
                            <th>Страна отправления</th>
                            <td><?= htmlspecialchars($arResult['DATA']['fromCountry']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Дата создания</th>
                            <td><?= htmlspecialchars($arResult['DATA']['trackCreationDateTime']) ?></td>
                        </tr>
                        <tr>
                            <th>Последнее обновление</th>
                            <td><?= htmlspecialchars($arResult['DATA']['trackUpdateDateTime']) ?></td>
                        </tr>
                        <tr>
                            <th>Дней в пути</th>
                            <td><mark><?= htmlspecialchars($arResult['DATA']['daysInTransit']) ?></mark></td>
                        </tr>
                        <?php if ($arResult['DATA']['deliveredStatus'] == '1'): ?>
                        <tr>
                            <th>Статус</th>
                            <td><span class="badge badge-success">Доставлено</span></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <?php if ($arResult['SHOW_STATISTICS'] === 'Y' && $arResult['DELIVERY_STAT']): ?>
                <!-- Статистика доставки -->
                <div class="track24-tracker__stats">
                    <h3>Статистика доставки</h3>
                    <table class="table table-bordered">
                        <tr>
                            <th>Тип отправления</th>
                            <td><?= htmlspecialchars($arResult['DELIVERY_STAT']['type']) ?></td>
                        </tr>
                        <tr>
                            <th>Минимальный срок</th>
                            <td><?= htmlspecialchars($arResult['DELIVERY_STAT']['minDeliveryDays']) ?> дней</td>
                        </tr>
                        <tr>
                            <th>Средний срок</th>
                            <td><?= htmlspecialchars($arResult['DELIVERY_STAT']['averageDeliveryDays']) ?> дней</td>
                        </tr>
                        <tr>
                            <th>Максимальный срок</th>
                            <td><?= htmlspecialchars($arResult['DELIVERY_STAT']['maxDeliveryDays']) ?> дней</td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>

                <!-- События отслеживания -->
                <?php if (!empty($arResult['DATA']['groupedEvents'])): ?>
                <div class="track24-tracker__events">
                    <h3>История перемещения</h3>
                    <div class="timeline">
                        <?php foreach ($arResult['DATA']['groupedEvents'] as $eventGroup): ?>
                            <?php foreach ($eventGroup as $event): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">
                                            <strong><?= htmlspecialchars($event['eventDateTime']) ?></strong>
                                        </div>
                                        <div class="timeline-type">
                                            <?= htmlspecialchars($event['operationTypeTranslated']) ?>
                                        </div>
                                        <div class="timeline-attribute">
                                            <?= htmlspecialchars($event['operationAttributeTranslated']) ?>
                                        </div>
                                        <?php if (!empty($event['serviceName'])): ?>
                                        <div class="timeline-service">
                                            <small class="text-muted">Сервис: <?= htmlspecialchars($event['serviceName']) ?></small>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Кнопка обновить -->
                <div class="track24-tracker__action">
                    <a href="?track_code=<?= urlencode($arResult['TRACK_CODE']) ?>" class="ui-btn ui-btn--gradient">Обновить информацию</a>
                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>
</section>
<!-- Track24 Tracker :: End -->

<?php if ($arResult['AUTO_UPDATE'] > 0): ?>
<script>
    setTimeout(function() {
        location.reload();
    }, <?= intval($arResult['AUTO_UPDATE']) * 1000 ?>);
</script>
<?php endif; ?>