USE yeticave;

/* Добавление категорий и их кодов */
INSERT INTO category
SET name = 'Доски и лыжи', code = 'boards', id = '1';
INSERT INTO category
SET name = 'Крепления', code = 'attachment', id = '2';
INSERT INTO category
SET name = 'Ботинки', code = 'boots', id = '3';
INSERT INTO category
SET name = 'Одежда', code = 'clothing', id = '4';
INSERT INTO category
SET name = 'Инструменты', code = 'tools', id = '5';
INSERT INTO category
SET name = 'Разное', code = 'other', id = '6';

/* Добавление тестовых юзеров */
INSERT INTO user
SET id = '1', user_name = 'Ivan', email = 'ivan@mailpost.ru', password = 'qwerty123',
	contact = '8-911-255-23-15';

INSERT INTO user
SET id = '2', user_name = 'Maria', email = 'maria@mailpost.ru', password = 'qwerty321',
	contact = '8-915-288-18-19';
	
INSERT INTO user
SET id = '3', user_name = 'Anna', email = 'anna@mailpost.ru', password = 'ytrewq321',
	contact = 'Пишите в главпочтамт до востребования';
	
/* Добавление существующих объявлений */
INSERT INTO lot
	SET id = '1',
	name = '2014 Rossignol District Snowboard',
	description = 'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами.',
	img = 'img/lot-1.jpg',
	start_price = '10999',
	price = '14999',
	dt_finish = '2019-09-01',
	rate_step = '2000',
	category_id = '1',
	autor_id = '1';
	
INSERT INTO lot
	SET id = '2',
	name = 'DC Ply Mens 2016/2017 Snowboard',
	description = 'Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости.',
	img = 'img/lot-2.jpg',
	start_price = '15999',
	price = '15999',
	dt_finish = '2019-12-02',
	rate_step = '3000',
	category_id = '1',
	autor_id = '2';
	
INSERT INTO lot
	SET id = '3',
	name = 'Крепления Union Contact Pro 2015 года размер L/XL',
	description = 'Если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
	img = 'img/lot-3.jpg',
	start_price = '8000',
	price = '8000',
	dt_finish = '2019-08-25',
	rate_step = '500',
	category_id = '2',
	autor_id = '1';

INSERT INTO lot
	SET id = '4',
	name = 'Ботинки для сноуборда DC Mutiny Charocal',
	description = 'Ботинки, две штуки, правый и левый. Очень, знаете ли, удобно, когда ботинки разные, а не два правых.',
	img = 'img/lot-4.jpg',
	start_price = '10999',
	price = '10999',
	dt_finish = '2019-11-11',
	rate_step = '800',
	category_id = '3',
	autor_id = '2';
	
INSERT INTO lot
	SET id = '5',
	name = 'Куртка для сноуборда DC Mutiny Charocal',
	description = 'Легкая, теплая куртка 2 в 1, выполнена из ткани с красивым стильным рисунком. По переду имеются карманы на скрытых молниях.',
	img = 'img/lot-5.jpg',
	start_price = '7500',
	price = '7500',
	dt_finish = '2020-09-01',
	rate_step = '100',
	category_id = '4',
	autor_id = '1';
	
INSERT INTO lot
	SET id = '6',
	name = 'Маска Oakley Canopy',
	description = 'Комфортная детская горнолыжная маска Carvy 2.0 оснащена однослойной линзой высокой контрастности. Делает восприятие склона максимально точным и детальным.',
	img = 'img/lot-6.jpg',
	start_price = '5400',
	price = '5400',
	dt_finish = '2019-08-20',
	rate_step = '100',
	category_id = '6',
	autor_id = '2';	
	
/* Добавляем ставки в первое объявление */
INSERT INTO rate
	SET id = '1',
	bid = '12999',
	user_id = '3',
	lot_id = '1';
	
INSERT INTO rate
	SET id = '2',
	bid = '14999',
	user_id = '2',
	lot_id = '1';
	
/* Получаем все категории */
SELECT name FROM category;

/* Получаем новые лоты*/
SELECT l.name AS lot_name, c.name AS category_name, start_price, price, img FROM lot l
JOIN category c
ON l.category_id = c.id  WHERE dt_finish > NOW()
ORDER BY created_at DESC;

/* Показать лот по его id */
SELECT l.id, l.name AS lot_name, c.name AS category_name FROM lot l
JOIN category c ON l.category_id = c.id
WHERE l.id = '1';

/* Обновляем название лота по его id */
UPDATE lot SET name = 'Горнолыжная маска Oakley Canopy'
WHERE id = '6';

/* Получаем список ставок с сортировкой по дате */
SELECT r.created_at, r.bid, l.name AS lot_name, u.user_name FROM rate r
JOIN user u ON u.id = r.user_id
JOIN lot l ON l.id = r.lot_id
ORDER BY r.created_at DESC;