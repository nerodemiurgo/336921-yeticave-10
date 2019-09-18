<div class="container">
    <section class="lots">
        <h2>Все лоты в категории «<span><?= htmlspecialchars($mycat['name']); ?></span>»</h2>
        <ul class="lots__list">

            <?php if (empty($lots)): ?>
                <p>В этой категории нет лотов.</p>
            <?php endif; ?>

            <?php if (!empty($search)): ?>
                <?php foreach ($lots as $item): ?>
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
                                    <span class="lot__amount"><?= htmlspecialchars($item['rates']) ?></span>
                                    <span
                                        class="lot__cost"><?= htmlspecialchars(decorate_price($item['price'])); ?></span>
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
            <?php endif; ?>
        </ul>
    </section>

    <ul class="pagination-list">
        <?php if ($pages_count > 1): ?>
            <li class="pagination-item pagination-item-prev"><a
                    href="/all-lots.php?cat=<?= htmlspecialchars($search); ?>&page=<?= ($cur_page - 1); ?>">Назад</a>
            </li>
            <?php foreach ($pages as $page): ?>
                <li class="pagination-item <?php if ($page == $cur_page): ?>pagination-item-active<?php endif; ?>">
                    <a href="/all-lots.php?cat=<?= htmlspecialchars($search); ?>&page=<?= $page; ?>"><?= $page; ?></a>
                </li>
            <?php endforeach; ?>
            <li class="pagination-item pagination-item-next"><a
                    href="/all-lots.php?cat=<?= htmlspecialchars($search); ?>&page=<?= ($cur_page + 1); ?>">Вперед</a>
            </li>
        <?php endif; ?>
    </ul>

</div>
