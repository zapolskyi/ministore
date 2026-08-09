# Як читати шаблони WordPress і WooCommerce

Довідник для роботи з чужим кодом шаблонів. Не підручник з PHP — тільки те,
що реально зустрічається щодня.

---

## 1. Конструкцій усього вісім

Відкрий будь-який шаблон Woo — там буде тільки це. Вивчивши таблицю, ти зможеш
читати **будь-який** файл у `templates/`.

| Конструкція | Що робить | Аналог у JS |
|---|---|---|
| `<?php echo $x; ?>` | надрукувати значення | `${x}` у шаблонному рядку |
| `foreach ( $arr as $item ) : … endforeach;` | цикл по масиву | `arr.forEach()` |
| `foreach ( $arr as $key => $value ) :` | цикл із ключем | `Object.entries()` |
| `if ( … ) : … else : … endif;` | умова | `if / else` |
| `$object->method()` | виклик методу об'єкта | `object.method()` |
| `esc_html()` / `esc_attr()` / `esc_url()` | екранування перед виводом | немає, робиться вручну |
| `do_action( 'ім’я' )` | точка розширення | `dispatchEvent()` |
| `apply_filters( 'ім’я', $значення )` | дозволити змінити значення | немає прямого аналога |

### Чому в шаблонах `endforeach`, а не `}`

```php
<?php foreach ( $items as $item ) : ?>
	<div>сто рядків HTML</div>
<?php endforeach; ?>
```

Коли між відкриттям і закриттям багато розмітки, самотня `}` унизу нічого не
пояснює. `endforeach;` одразу каже, що саме закрилось. У шаблонах пишуть тільки так,
у звичайному PHP-коді — фігурні дужки.

### Пастки для тих, хто прийшов з JS

| PHP | JS | Різниця |
|---|---|---|
| `.` | `+` | склеювання рядків: `'a' . 'b'` |
| `->` | `.` | звернення до методу об'єкта |
| `=>` | `:` | пара «ключ — значення» в масиві |
| `$var` | `var` | змінні завжди з `$` |
| `global $product;` | — | без цього рядка глобальна змінна не видима у файлі |

---

## 2. Як дізнатися, що робить незнайома функція

### Крок 1. Префікс скаже, чиє це

| Префікс | Джерело | Де шукати |
|---|---|---|
| `wc_`, `woocommerce_` | WooCommerce | `plugins/woocommerce/includes/` |
| `wp_`, `get_`, `the_`, `esc_`, `is_` | ядро WordPress | `wp-includes/` |
| `mytheme_` | наша тема | `inc/` |

### Крок 2. Знайти оголошення

```bash
cd "app/public/wp-content/plugins/woocommerce"
grep -rn "function wc_dropdown_variation_attribute_options" includes/
```

У WordPress так само:

```bash
cd "app/public"
grep -rn "function wc_get_template\b" wp-includes/ wp-content/plugins/woocommerce/
```

### Крок 3. Найшвидший спосіб — надрукувати результат

Один рядок відповідає на більше питань, ніж година читання документації:

```php
echo '<pre>';
print_r( $product->get_attributes() );
echo '</pre>';
```

Встав це прямо в шаблон, онови сторінку — і побачиш точну структуру даних.
Потім прибери. **Не комітити.**

Зручні цілі для друку в шаблонах Woo: `$product`, `$attributes`,
`$available_variations`, `$args`.

---

## 3. Як зрозуміти, який шаблон малює сторінку

### Спосіб 1. Клас `<body>`

Подивись у DevTools:

```html
<body class="archive post-type-archive post-type-archive-product woocommerce-shop">
```

`post-type-archive-product` → малює `archive-product.php`.
`single-product` → `single-product.php`.

### Спосіб 2. Список у WooCommerce

**WooCommerce → Статус → вкладка «Шаблони»**

Показує всі шаблони, перевизначені темою, і — найцінніше — **червоним підсвічує
ті, що відстали від версії плагіна**. Це перше місце, куди дивитись після
оновлення WooCommerce.

### Спосіб 3. Ієрархія шаблонів товарів

| Сторінка | Файл |
|---|---|
| Каталог, категорії, теги, бренди, пошук по товарах | `archive-product.php` |
| Одна картка в сітці | `content-product.php` |
| Сторінка товару | `single-product.php` |
| Форма «в кошик», простий товар | `single-product/add-to-cart/simple.php` |
| Форма «в кошик», варіативний | `single-product/add-to-cart/variable.php` |
| Кошик | `cart/cart.php` |
| Оформлення | `checkout/form-checkout.php` |

Правило: усе, що починається на `content-`, — це **вміст усередині чогось**,
а не окрема сторінка.

---

## 4. Порядок дій, коли треба щось змінити

Завжди згори вниз, оверрайд — останній варіант:

**1. Чи є хук поруч?**

```bash
grep -n "do_action" plugins/woocommerce/templates/global/quantity-input.php
```

Так ми додали кнопки `−` і `+` до поля кількості — без копіювання шаблону,
через `woocommerce_before_quantity_input_field`.

**2. Чи є фільтр значення?**

```bash
grep -rn "apply_filters( 'woocommerce_loop_add_to_cart_link'" plugins/woocommerce/
```

**3. Тільки тоді оверрайд.**

---

## 5. Правила оверрайду

| Правило | Чому |
|---|---|
| **Копіювати, а не переміщувати** | інакше плагін лишається без файлу; при вимкненні теми функція зникне |
| **Не чіпати папку плагіна** | оновлення все одно перезапише |
| **Лишити `@version` у шапці** | за нею видно, наскільки копія відстала |
| **Зберегти всі `do_action` і `apply_filters`** | через них вбудовуються плагіни |
| **Зберегти класи, за якими чіпляється JS** | напр. `.variations`, `.reset_variations`, `.qty` |
| **Записати файл у реєстр** | [DECISIONS.md](DECISIONS.md) → «Реєстр оверрайдів» |
| **Жодних порожніх файлів у `woocommerce/`** | порожній файл = порожній вивід **без помилки** |

### Як перевірити, що JS чогось не зламається

Перед тим як прибрати клас або тег зі шаблону:

```bash
grep -n "className\|\.variations\|find(" plugins/woocommerce/assets/js/frontend/add-to-cart-variation.js | head
```

Якщо селектор згадується в скрипті — його чіпати не можна.

---

## 6. Що робити після оновлення WooCommerce

1. **WooCommerce → Статус → Шаблони** — подивитись, що підсвічено червоним
2. Для кожного застарілого: `diff` між нашим файлом і новим оригіналом
3. Перенести зміни Woo у свою копію, оновити `@version`
4. Позначити дату звірки в [DECISIONS.md](DECISIONS.md)

```bash
diff -u \
  "plugins/woocommerce/templates/single-product/add-to-cart/variable.php" \
  "themes/my-theme/woocommerce/single-product/add-to-cart/variable.php"
```
