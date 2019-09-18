<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное
        снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $item): ?>
            <li class="promo__item promo__item--<?= htmlspecialchars($item['code']); ?>">
                <a class="promo__link"
                   href="/all-lots.php?cat=<?= htmlspecialchars($item['code']); ?>"><?= htmlspecialchars($item['name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($lots_list as $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="/uploads/<?= htmlspecialchars($item['img']); ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($item['category_name']); ?></span>
                    <h3 class="lot__title"><a class="text-link"
                                              href="/lot.php?id=<?= htmlspecialchars($item['lot_id']); ?>"><?= htmlspecialchars($item['lot_name']); ?></a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= htmlspecialchars(decorate_price($item['price'])); ?></span>
                        </div>
                        <?php $timeend = timeuptoend($item['dt_finish']);
                        if ($timeend[0] > 0): ?>
                            <div class="lot__timer timer">
                                <?= $timeend[0] . ':' . $timeend[1]; ?>
                            </div>
                        <?php else : ?>
                            <div class="lot__timer timer timer--finishing">
                                <?= $timeend[0] . ':' . $timeend[1]; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
