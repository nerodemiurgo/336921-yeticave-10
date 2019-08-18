    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
			<?php foreach ($categories as $item): ?>
				<li class="promo__item promo__item--boards">
					<a class="promo__link" href="pages/all-lots.html"><?=htmlspecialchars($item); ?></a>
				</li>
			<?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
			<?php foreach ($lots_list as $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=htmlspecialchars($item['img_url']);?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($item['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=htmlspecialchars($item['lot_name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=htmlspecialchars(decorate_price($item['price'])); ?></span>
                        </div>
						<?php $timeend = timeuptoend(htmlspecialchars($item['time_end']));
                        if ($timeend[0] > 0): ?>
							<div class="lot__timer timer">
								<?=$timeend[0] . ':' . $timeend[1];?> 
							</div>
						<?php else : ?>
							<div class="lot__timer timer timer——finishing" style="background: #f84646;">
								<?=$timeend[0] . ':' . $timeend[1];?> 
							</div>
						<?php endif; ?>
                    </div>
                </div>
            </li>
			<?php endforeach; ?>
        </ul>
    </section>