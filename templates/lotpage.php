    <section class="lot-item container">
      <h2><?=htmlspecialchars($lot_info['lot_name']); ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="/uploads/<?=htmlspecialchars($lot_info['img']); ?>" width="730" height="548" alt="<?=htmlspecialchars($lot_info['lot_name']); ?>">
          </div>
          <p class="lot-item__category">Категория: <span><?=htmlspecialchars($lot_info['category_name']); ?></span></p>
          <p class="lot-item__description"><?=htmlspecialchars($lot_info['lot_desc']); ?></p>
        </div>
        <div class="lot-item__right">
          <div class="lot-item__state">
			<?php $timeend = timeuptoend($lot_info['dt_finish']);?>
					<div class="lot-item__timer timer <?= $timeend[0] < 1 ? 'timer--finishing' : '' ?>">
						<?=$timeend[0] . ':' . $timeend[1];?> 
					</div>		
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=decorate_price(htmlspecialchars($lot_info['price'])); ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=decorate_price(htmlspecialchars($lot_info['rate_step'])); ?></span>
              </div>
            </div>
			<?php if ($canseebets === true): ?>
				<form class="lot-item__form" method="post" autocomplete="off">
				  <p class="lot-item__form-item form__item <?= empty($errors['bid']) ?: 'form__item--invalid' ?>">
					<label for="bid">Ваша ставка</label>
					<input id="bid" type="text" name="bid" placeholder="<?=htmlspecialchars($lot_info['price']+htmlspecialchars($lot_info['rate_step'])); ?>">
					  <?php if (isset($errors['bid'])) : ?>
						 <span class="form__error"><?= $errors['bid'] ?></span>
					  <?php endif; ?>
				  </p>
				  <button type="submit" class="button">Сделать ставку</button>
				</form>
			<?php endif; ?>
          </div>
		  <?php $countrate = count($rates); if ($countrate !== 0) : ?>
			  <div class="history">
				<h3>История ставок (<span><?=$countrate; ?></span>)</h3>
				<table class="history__list">
				<?php foreach ($rates as $item): ?>
				  <tr class="history__item">
					<td class="history__name"><?=htmlspecialchars($item['user_name']); ?></td>
					<td class="history__price"><?=decorate_price(htmlspecialchars($item['bid'])); ?></td>
					<td class="history__time"><?=timeFromBet(htmlspecialchars($item['time'])); ?></td>
				  </tr>
				<?php endforeach; ?>
				</table>
			  </div>
		  <?php endif; ?>
        </div>
      </div>
    </section>