<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);
$componentPath = $this->GetFolder();
?>

<!-- Track24 Tracker :: Start -->
<section class="track24-tracker">
    <div class="container-fluid">
        
        <?php if ($arResult['SHOW_TITLE'] === 'Y'): ?>
            <div class="track24-tracker__header">
                <h2 class="track24-tracker__title"><?= htmlspecialchars($arResult['TITLE']) ?></h2>
            </div>
        <?php endif; ?>

        <!-- Форма ввода трек-кода -->
        <div class="track24-tracker__form">
            <form id="track24-form">
                <div class="form-group">
                    <label for="track-code-input">Введите трек-номер:</label>
                    <input type="text" 
                           id="track-code-input" 
                           name="track_code" 
                           class="form-control" 
                           placeholder="Например: LC166805240CN"
                           value="<?= htmlspecialchars($arResult['TRACK_CODE']) ?>"
                           required>
                </div>
                <button type="submit" class="ui-btn ui-btn--gradient" id="track24-submit">
                    <span class="track24-btn-text">Отследить посылку</span>
                    <span class="track24-spinner" style="display: none;">Загрузка...</span>
                </button>
            </form>
        </div>

        <!-- Результаты -->
        <div id="track24-results" style="margin-top: 30px;">
            <?php if ($arResult['ERROR']): ?>
                <!-- Ошибка -->
                <div class="alert alert-danger">
                    <strong>Ошибка:</strong> <?= htmlspecialchars($arResult['ERROR']) ?>
                    
                    <?php if (!empty($arResult['ERROR_DETAILS'])): ?>
                        <details style="margin-top: 15px;">
                            <summary style="cursor: pointer; font-weight: 600;">Подробная информация об ошибке</summary>
                            <div style="margin-top: 10px; padding: 10px; background: #fff; border-radius: 4px;">
                                <?php if (isset($arResult['ERROR_DETAILS']['http_code'])): ?>
                                    <p><strong>HTTP код:</strong> <?= htmlspecialchars($arResult['ERROR_DETAILS']['http_code']) ?></p>
                                <?php endif; ?>
                                <?php if (isset($arResult['ERROR_DETAILS']['raw_response'])): ?>
                                    <p><strong>Ответ API:</strong></p>
                                    <pre style="background: #f5f5f5; padding: 10px; overflow-x: auto; font-size: 12px;"><?= htmlspecialchars($arResult['ERROR_DETAILS']['raw_response']) ?></pre>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endif; ?>
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
                <?php
                $events = [];
                if (!empty($arResult['DATA']['groupedEvents']) && is_array($arResult['DATA']['groupedEvents'])) {
                    // Если groupedEvents - массив, используем его
                    foreach ($arResult['DATA']['groupedEvents'] as $eventGroup) {
                        if (is_array($eventGroup)) {
                            $events = array_merge($events, $eventGroup);
                        }
                    }
                } elseif (!empty($arResult['DATA']['events']) && is_array($arResult['DATA']['events'])) {
                    // Иначе используем events напрямую
                    $events = $arResult['DATA']['events'];
                }
                
                // Сортируем события по дате (новые сверху)
                usort($events, function($a, $b) {
                    return strtotime($b['operationDateTime']) - strtotime($a['operationDateTime']);
                });
                ?>
                
                <?php if (!empty($events)): ?>
                <div class="track24-tracker__events">
                    <h3>История перемещения</h3>
                    <div class="timeline">
                        <?php foreach ($events as $event): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker <?= $event['icon'] ? 'timeline-marker--' . htmlspecialchars($event['icon']) : '' ?>"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date">
                                        <strong><?= htmlspecialchars($event['operationDateTime']) ?></strong>
                                    </div>
                                    <?php if (!empty($event['operationAttributeTranslated'])): ?>
                                    <div class="timeline-type">
                                        <?= htmlspecialchars($event['operationAttributeTranslated']) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($event['operationPlaceNameTranslated']) || !empty($event['operationPlaceName'])): ?>
                                    <div class="timeline-place">
                                        📍 <?= htmlspecialchars($event['operationPlaceNameTranslated'] ?: $event['operationPlaceName']) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($event['serviceName'])): ?>
                                    <div class="timeline-service">
                                        <small class="text-muted">Сервис: <?= htmlspecialchars($event['serviceName']) ?></small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Кнопка обновить -->
                <div class="track24-tracker__action">
                    <button type="button" class="ui-btn ui-btn--gradient" id="track24-refresh">Обновить информацию</button>
                </div>

            <?php endif; ?>
        </div>

    </div>
</section>
<!-- Track24 Tracker :: End -->

<script>
(function() {
    const form = document.getElementById('track24-form');
    const submitBtn = document.getElementById('track24-submit');
    const btnText = submitBtn.querySelector('.track24-btn-text');
    const spinner = submitBtn.querySelector('.track24-spinner');
    const resultsDiv = document.getElementById('track24-results');
    const trackInput = document.getElementById('track-code-input');
    
    function setLoading(isLoading) {
        if (isLoading) {
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            spinner.style.display = 'inline';
        } else {
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            spinner.style.display = 'none';
        }
    }
    
    function fetchTracking(trackCode) {
        setLoading(true);
        
        const url = 'https://api.track24.ru/tracking.json.php?code=' + 
                    encodeURIComponent(trackCode) + 
                    '&domain=<?= htmlspecialchars($arResult['DOMAIN']) ?>';
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'ok') {
                    let errorMsg = 'API вернул ошибку в ответе';
                    if (data.error) errorMsg += ': ' + data.error;
                    if (data.message) errorMsg += ' - ' + data.message;
                    
                    resultsDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Ошибка:</strong> ${escapeHtml(errorMsg)}
                            <details style="margin-top: 15px;">
                                <summary style="cursor: pointer; font-weight: 600;">Подробная информация об ошибке</summary>
                                <div style="margin-top: 10px; padding: 10px; background: #fff; border-radius: 4px;">
                                    <p><strong>Ответ API:</strong></p>
                                    <pre style="background: #f5f5f5; padding: 10px; overflow-x: auto; font-size: 12px;">${escapeHtml(JSON.stringify(data, null, 2))}</pre>
                                </div>
                            </details>
                        </div>
                    `;
                    return;
                }
                
                renderResults(data);
            })
            .catch(error => {
                resultsDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Ошибка:</strong> ${escapeHtml(error.message)}
                    </div>
                `;
            })
            .finally(() => {
                setLoading(false);
            });
    }
    
    function renderResults(data) {
        const trackData = data.data;
        const deliveryStat = data.deliveredStat;
        
        let html = `
            <div class="track24-tracker__info">
                <table class="table table-bordered">
                    <tr>
                        <th>Трек-номер</th>
                        <td><strong>${escapeHtml(trackData.trackCode)}</strong></td>
                    </tr>
                    ${trackData.fromCountry ? `
                    <tr>
                        <th>Страна отправления</th>
                        <td>${escapeHtml(trackData.fromCountry)}</td>
                    </tr>
                    ` : ''}
                    <tr>
                        <th>Дата создания</th>
                        <td>${escapeHtml(trackData.trackCreationDateTime)}</td>
                    </tr>
                    <tr>
                        <th>Последнее обновление</th>
                        <td>${escapeHtml(trackData.trackUpdateDateTime)}</td>
                    </tr>
                    <tr>
                        <th>Дней в пути</th>
                        <td><mark>${escapeHtml(trackData.daysInTransit)}</mark></td>
                    </tr>
                    ${trackData.deliveredStatus == '1' ? `
                    <tr>
                        <th>Статус</th>
                        <td><span class="badge badge-success">Доставлено</span></td>
                    </tr>
                    ` : ''}
                </table>
            </div>
        `;
        
        <?php if ($arResult['SHOW_STATISTICS'] === 'Y'): ?>
        if (deliveryStat) {
            html += `
                <div class="track24-tracker__stats">
                    <h3>Статистика доставки</h3>
                    <table class="table table-bordered">
                        <tr>
                            <th>Тип отправления</th>
                            <td>${escapeHtml(deliveryStat.type)}</td>
                        </tr>
                        <tr>
                            <th>Минимальный срок</th>
                            <td>${escapeHtml(deliveryStat.minDeliveryDays)} дней</td>
                        </tr>
                        <tr>
                            <th>Средний срок</th>
                            <td>${escapeHtml(deliveryStat.averageDeliveryDays)} дней</td>
                        </tr>
                        <tr>
                            <th>Максимальный срок</th>
                            <td>${escapeHtml(deliveryStat.maxDeliveryDays)} дней</td>
                        </tr>
                    </table>
                </div>
            `;
        }
        <?php endif; ?>
        
        if (trackData.groupedEvents && trackData.groupedEvents.length > 0) {
            html += '<div class="track24-tracker__events"><h3>История перемещения</h3><div class="timeline">';
            
            trackData.groupedEvents.forEach(eventGroup => {
                eventGroup.forEach(event => {
                    html += renderEvent(event);
                });
            });
            
            html += '</div></div>';
        } else if (trackData.events && trackData.events.length > 0) {
            // Если groupedEvents пустой, используем events
            html += '<div class="track24-tracker__events"><h3>История перемещения</h3><div class="timeline">';
            
            // Сортируем события по дате (новые сверху)
            const sortedEvents = [...trackData.events].sort((a, b) => {
                return new Date(b.operationDateTime) - new Date(a.operationDateTime);
            });
            
            sortedEvents.forEach(event => {
                html += renderEvent(event);
            });
            
            html += '</div></div>';
        }
        
        html += `
            <div class="track24-tracker__action">
                <button type="button" class="ui-btn ui-btn--gradient" id="track24-refresh">Обновить информацию</button>
            </div>
        `;
        
        resultsDiv.innerHTML = html;
        
        // Добавляем обработчик на кнопку обновления
        document.getElementById('track24-refresh').addEventListener('click', function() {
            fetchTracking(trackInput.value);
        });
    }
    
    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    
    function renderEvent(event) {
        const markerClass = event.icon ? `timeline-marker--${event.icon}` : '';
        const placeName = event.operationPlaceNameTranslated || event.operationPlaceName || '';
        
        return `
            <div class="timeline-item">
                <div class="timeline-marker ${markerClass}"></div>
                <div class="timeline-content">
                    <div class="timeline-date">
                        <strong>${escapeHtml(event.operationDateTime)}</strong>
                    </div>
                    ${event.operationAttributeTranslated ? `
                    <div class="timeline-type">
                        ${escapeHtml(event.operationAttributeTranslated)}
                    </div>
                    ` : ''}
                    ${placeName ? `
                    <div class="timeline-place">
                        📍 ${escapeHtml(placeName)}
                    </div>
                    ` : ''}
                    ${event.serviceName ? `
                    <div class="timeline-service">
                        <small class="text-muted">Сервис: ${escapeHtml(event.serviceName)}</small>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const trackCode = trackInput.value.trim();
        if (trackCode) {
            fetchTracking(trackCode);
        }
    });
    
    // Обработчик для кнопки обновления (если уже есть результаты)
    const refreshBtn = document.getElementById('track24-refresh');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            fetchTracking(trackInput.value);
        });
    }
    
    <?php if ($arResult['AUTO_UPDATE'] > 0): ?>
    // Автообновление
    setInterval(function() {
        if (trackInput.value.trim()) {
            fetchTracking(trackInput.value);
        }
    }, <?= intval($arResult['AUTO_UPDATE']) * 1000 ?>);
    <?php endif; ?>
})();
</script>