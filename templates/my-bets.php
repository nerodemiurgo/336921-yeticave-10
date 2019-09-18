<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($lot_info as $item): ?>
            <tr class="rates__item <?= classToString(htmlspecialchars($item['dt_finish']), $item['winner_id']); ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="/uploads/<?= htmlspecialchars($item['img']); ?>" width="54" height="40"
                             alt="<?= htmlspecialchars($item['lot_name']); ?>">
                    </div>
                    <h3 class="rates__title"><a
                            href="/lot.php?id=<?= htmlspecialchars($item['lot_id']); ?>"><?= htmlspecialchars($item['lot_name']); ?></a>
                    </h3>
                    <?php if (isset($item['winner_id'])): ?>
                        <p><?= htmlspecialchars($item['contact']); ?></p><?php endif; ?>
                </td>
                <td class="rates__category">
                    <?= htmlspecialchars($item['category_name']); ?>
                </td>
                <td class="rates__timer">
                    <?php $timeend = timeuptoend($item['dt_finish']);
                    if ($timeend[0] > 0): ?>
                        <div class="timer">
                            <?= $timeend[0] . ':' . $timeend[1]; ?>
                        </div>
                    <?php elseif (($timeend[0] === 0) && ($timeend[1] > 0)): ?>
                        <div class="timer timer--finishing">
                            <?= $timeend[0] . ':' . $timeend[1]; ?>
                        </div>
                    <?php elseif (($timeend[0] <= 0) && ($timeend[1] <= 0) && (isset($item['winner_id']))): ?>
                        <div class="timer timer--win">
                            Ставка выиграла
                        </div>
                    <?php elseif (($timeend[0] <= 0) && ($timeend[1] <= 0) && (!isset($item['winner_id']))): ?>
                        <div class="timer timer--end">
                            Торги окончены
                        </div>
                    <?php endif; ?>
                </td>
                <td class="rates__price">
                    <?= htmlspecialchars(decorate_price($item['price'])); ?>
                </td>
                <td class="rates__time">
                    <?= timeFromBet(htmlspecialchars($item['time'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
