# Стандарти коду — claude-store

Читати **перед** написанням коду. Мета — щоб через три місяці файл виглядав так,
ніби його писала та сама людина, що й сьогодні.

---

## PHP

### Обов'язкове

```php
<?php
/**
 * Картка товару в каталозі.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;
```

- Захист `ABSPATH` — на початку **кожного** PHP-файлу. Без винятків
- Стандарт: [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Табуляція для відступів, пробіли всередині дужок: `function foo( $arg )`
- Yoda-умови: `if ( 'value' === $var )`
- Іменування: функції та змінні — `snake_case`, класи — `Capital_Snake_Case`

### Префікси

Усе глобальне — з префіксом `mytheme_`. Це не педантизм: без префікса функція
`get_product_price()` рано чи пізно зіткнеться з такою ж із плагіна, і сайт впаде
з fatal error.

```php
function mytheme_get_product_badge( $product ) { … }

add_action( 'init', 'mytheme_register_menus' );
```

### Екранування 🔴

Правило просте: **екранується все, що виводиться, у момент виводу.**

```php
echo esc_html( $title );
echo esc_attr( $class );
echo esc_url( $link );
echo wp_kses_post( $description );   // якщо потрібен HTML
printf( esc_html__( 'Знайдено %d товарів', 'my-theme' ), (int) $count );
```

Виняток — те, що вже екранував WooCommerce (`wc_price()`, `$product->get_price_html()`).

Вхідні дані — навпаки, санітизуються:

```php
$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$id     = absint( $_GET['product_id'] ?? 0 );
```

### AJAX і форми 🔴

```php
// віддача
wp_nonce_field( 'mytheme_add_to_cart', 'mytheme_nonce' );

// приймання
if ( ! isset( $_POST['mytheme_nonce'] )
     || ! wp_verify_nonce( sanitize_key( $_POST['mytheme_nonce'] ), 'mytheme_add_to_cart' ) ) {
    wp_send_json_error( [ 'message' => __( 'Помилка перевірки. Оновіть сторінку.', 'my-theme' ) ] );
}
```

Nonce обов'язковий на будь-якій дії, що змінює стан. Плюс перевірка прав
(`current_user_can()`), якщо дія не для гостей.

### Запити до бази

- `WP_Query` / `wc_get_products()`, не сирий SQL
- Заборонено: `posts_per_page => -1` на товарах. На каталозі в 1000 SKU це тайм-аут
- `'no_found_rows' => true`, якщо пагінація не потрібна
- Важкі запити — у транзієнти:

```php
$featured = get_transient( 'mytheme_featured_products' );
if ( false === $featured ) {
    $featured = wc_get_products( [ 'featured' => true, 'limit' => 8 ] );
    set_transient( 'mytheme_featured_products', $featured, HOUR_IN_SECONDS );
}
```

- Транзієнт із товарами — інвалідувати на `save_post_product` і `woocommerce_update_product`

### Структура

- Файл довший за ~300 рядків — привід розбити
- Функція довша за ~50 рядків — привід винести частину
- `functions.php` — тільки константи й `require`. Логіки там немає
- Логіка виводу секції живе в `template-parts/`, не в `inc/`

### Заборонено

- Правки ядра WordPress і WooCommerce
- `query_posts()`
- `@` для придушення помилок
- `eval()`, `extract()`
- `error_reporting(0)`
- `var_dump` / `print_r` / `console.log` у коміті
- Ключі, паролі, токени в коді

---

## SCSS

### Структура (7-1)

```
abstracts/   змінні, токени, функції, міксини, плейсхолдери — не дає CSS на виході
base/        reset, типографіка, хелпери
layout/      контейнер, сітка, хедер, підвал, навігація
components/  перевикористовувані блоки: кнопки, картки, форми, хлібні крихти
sections/    секції головної — по файлу на секцію
pages/       специфіка окремих сторінок
shop/        усе, що стосується WooCommerce
vendors/     стороннє
```

Точки входу — `style.scss` і `woocommerce.scss`, обидві через `@use` (не `@import`,
він застарілий).

### BEM

```scss
.product-card { }
.product-card__image { }
.product-card__title { }
.product-card--sale { }
```

- Один файл = один блок
- Вкладеність — **максимум 3 рівні**. Глибше — сигнал, що розмітка складна
- `&__element` всередині блока — так; `&--modifier` — так; `.a .b .c .d` — ні

### Токени

Кольори, шрифти, відступи, радіуси, тіні — **тільки** в `abstracts/`.
Хардкод `#e63946` у файлі компонента заборонений: коли замовник попросить змінити
фірмовий колір, ми маємо правити одне місце, а не шукати по 40 файлах.

```scss
// abstracts/_variables.scss
$color-primary: #e63946;
$space-md: 1.5rem;

// components/_buttons.scss
.btn { background: $color-primary; padding: $space-md; }
```

### Адаптив

Mobile-first, брейкпоінти через міксин:

```scss
.product-grid {
    grid-template-columns: repeat(2, 1fr);

    @include respond-to('md') { grid-template-columns: repeat(3, 1fr); }
    @include respond-to('lg') { grid-template-columns: repeat(4, 1fr); }
}
```

Брейкпоінти: `sm` 576 · `md` 768 · `lg` 1024 · `xl` 1440.

### Заборонено

- `!important` (виняток — перебиття інлайнових стилів плагіна, з коментарем чому)
- Редагувати `assets/css/*.css` руками — це артефакт збірки
- Прив'язка стилів до тегів: `div > ul > li`
- Стилі за id
- Класи-утиліти, що дублюють компоненти

---

## JavaScript

- Ванільний JS, без jQuery у власному коді (jQuery тягне WooCommerce — це його справа)
- ES6+, `const`/`let`, без `var`
- Один `main.js`, розбитий на іменовані функції-модулі
- Ініціалізація через `DOMContentLoaded`
- Слухачі вішаємо лише якщо елемент існує:

```js
const burger = document.querySelector('.header__burger');
if (burger) { burger.addEventListener('click', toggleMenu); }
```

- AJAX-параметри — через `wp_localize_script()`, не хардкодом URL
- Обробка помилок обов'язкова: мережа падає, і користувач має це побачити
- Без `console.log` у продакшн-коді
- Скрипти з `defer`

---

## Іменування

| Що | Як | Приклад |
|---|---|---|
| PHP-файли теми | `kebab-case.php` | `product-card.php` |
| SCSS-партіали | `_kebab-case.scss` | `_product-card.scss` |
| PHP-функції | `mytheme_snake_case()` | `mytheme_get_cart_count()` |
| CSS-класи | BEM | `.product-card__price--old` |
| Поля SCF | `snake_case`, з префіксом групи | `hero_title`, `hero_button_url` |
| Хуки теми | `mytheme_*` | `mytheme_before_product_card` |
| Гілки git | `type/короткий-опис` | `feat/product-filters` |

---

## Git

### Коміти

Conventional Commits, тіло — українською:

```
feat(shop): фільтр каталогу за атрибутами
fix(checkout): валідація телефону приймала літери
refactor(scss): токени кольорів винесено в abstracts
chore(deps): оновлено sass до 1.80
docs: чекліст фази 4
style(header): вирівнювання іконки кошика
perf(catalog): транзієнт для хітів продажів
```

Типи: `feat` · `fix` · `refactor` · `style` · `perf` · `docs` · `chore` · `test`

**Правила:**

- Один коміт — одна логічна зміна. «Зробив усе за день» одним комітом неможливо відкотити
- Заголовок — до 72 символів, наказовий спосіб
- Не комітити зламаний код у `main`
- Не комітити ключі, паролі, `node_modules`, `.DS_Store`

### Гілки

| Гілка | Призначення |
|---|---|
| `main` | Продакшн. Завжди робоча. Тегується релізами |
| `dev` | Основна робота. Мерж у `main` = готовність до релізу |
| `feat/*`, `fix/*` | Великі окремі задачі, мерж у `dev` |

Теги релізів — семантичні: `v1.0.0`, `v1.1.0`, `v1.1.1`.

---

## Продуктивність

- Зображення: `loading="lazy"` на всіх, крім першого екрана
- 🔴 `width` і `height` на **кожному** `<img>` — без них стрибає розкладка (CLS)
- `srcset` через `wp_get_attachment_image()`, не самописний `<img>`
- Не запитувати товари, які не виводяться на сторінці
- Скрипти й стилі — тільки там, де потрібні
- Перед комітом важкої сторінки — глянути Query Monitor: скільки запитів і чи є повільні

---

## Доступність

- Семантичні теги: `<header>`, `<nav>`, `<main>`, `<footer>`, `<article>`
- Один `<h1>` на сторінку, ієрархія заголовків без пропусків
- `alt` на всіх змістовних зображеннях; декоративні — `alt=""`
- Контраст тексту ≥ 4.5:1
- Фокус видимий — не вимикати `outline` без заміни
- Бургер, кошик, акордеони — з `aria-expanded` і `aria-label`
- Форми: `<label>` прив'язаний до поля, помилки читаються скрінрідером

---

## Локалізація

Одна мова (українська), але тексти все одно проганяємо через функції перекладу —
це безкоштовно зараз і рятує, якщо колись додасться друга мова.

```php
esc_html_e( 'Додати в кошик', 'my-theme' );
esc_html__( 'Немає в наявності', 'my-theme' );
```

- Text domain: `my-theme` — однаковий скрізь
- Хардкод українського тексту прямо в розмітці — небажаний
- Ціни — через `wc_price()`, дати — через `date_i18n()`

---

## Перед комітом — швидка перевірка

- [ ] `defined( 'ABSPATH' ) || exit;` у нових PHP-файлах
- [ ] Увесь вивід екранований, вхід санітизований
- [ ] Nonce на діях, що змінюють стан
- [ ] Немає `console.log`, `var_dump`, закоментованого сміття
- [ ] Немає ключів і паролів
- [ ] SCSS зібрано (`npm run scss`), у CSS немає помилок
- [ ] Консоль браузера чиста
- [ ] `debug.log` без нових нотисів
- [ ] Перевірено на 375 і 1440
- [ ] Повідомлення коміту за форматом
