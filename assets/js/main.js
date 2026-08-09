/**
 * MiniStore — інтерактив теми.
 *
 * Ванільний JS, без jQuery. Кожна функція самостійна: спершу шукає свій елемент
 * і мовчки виходить, якщо його на сторінці немає. Тому один файл спокійно
 * працює і в каталозі, і на сторінці товару.
 */

(function () {
	'use strict';

	/**
	 * Бургер-меню: показує й ховає навігацію на мобільному.
	 *
	 * aria-expanded — не прикраса: скрінрідер із нього дізнається,
	 * відкрите меню чи закрите. Заодно за цим атрибутом працює анімація хрестика в CSS.
	 */
	function initBurger() {
		const burger = document.querySelector('.burger');
		const nav = document.querySelector('.site-nav');

		if (!burger || !nav) {
			return;
		}

		burger.addEventListener('click', function () {
			const isOpen = nav.classList.toggle('is-open');

			burger.setAttribute('aria-expanded', String(isOpen));
			burger.setAttribute('aria-label', isOpen ? 'Закрити меню' : 'Відкрити меню');
		});
	}

	/**
	 * Випадаюче підменю на мобільному.
	 *
	 * На десктопі його відкриває :hover у CSS, тому тут ми втручаємось лише
	 * на вузьких екранах, де наведення миші не існує.
	 */
	function initMobileSubmenu() {
		const parents = document.querySelectorAll('.site-nav__item--has-children');

		parents.forEach(function (parent) {
			const link = parent.querySelector('.site-nav__link');

			if (!link) {
				return;
			}

			link.addEventListener('click', function (event) {
				if (window.innerWidth >= 1024) {
					return; // десктоп — хай працює звичайний перехід за посиланням
				}

				event.preventDefault();
				parent.classList.toggle('site-nav__item--open');
			});
		});
	}

	/**
	 * Лічильник кількості товару: кнопки «−» і «+».
	 *
	 * Межі беремо з атрибутів min і max самого поля — щоб правило
	 * «не більше 23 штук» жило в розмітці, а не було зашите в скрипті.
	 */
	function initQuantity() {
		// Обгортку .quantity і поле .qty друкує WooCommerce,
		// кнопки додані через хуки в inc/woocommerce-setup.php.
		const widgets = document.querySelectorAll('.quantity');

		widgets.forEach(function (widget) {
			const input = widget.querySelector('input.qty');
			const minus = widget.querySelector('[data-quantity-minus]');
			const plus = widget.querySelector('[data-quantity-plus]');

			if (!input) {
				return;
			}

			const min = Number(input.min) || 1;
			const max = Number(input.max) || Infinity;

			function change(step) {
				const current = Number(input.value) || min;
				const next = Math.min(max, Math.max(min, current + step));

				input.value = next;

				// Повідомляємо решту сторінки, що значення змінилось.
				// Знадобиться, коли на цю подію підпишеться перерахунок кошика.
				input.dispatchEvent(new Event('change', { bubbles: true }));
			}

			if (minus) {
				minus.addEventListener('click', function () {
					change(-1);
				});
			}

			if (plus) {
				plus.addEventListener('click', function () {
					change(1);
				});
			}

			// Захист від ручного введення нісенітниці на кшталт 0 або 999.
			input.addEventListener('blur', function () {
				const value = Number(input.value);

				if (!Number.isFinite(value) || value < min) {
					input.value = min;
				} else if (value > max) {
					input.value = max;
				}
			});
		});
	}

	/**
	 * Свотчі варіацій — місток між нашими кнопками і формою WooCommerce.
	 *
	 * Логіку варіацій (яка ціна, яке фото, які комбінації доступні) рахує
	 * скрипт Woo add-to-cart-variation.js. Він читає ТІЛЬКИ <select>.
	 * Наші кнопки нічого не вирішують — вони лише записують значення в select.
	 *
	 * Потік даних односторонній, і це навмисно:
	 *
	 *   клік по кнопці → select.value → подія change → Woo перерахував →
	 *   → ми перемалювали кнопки за станом select
	 *
	 * Тобто джерело правди одне — select. Кнопки ніколи не зберігають стан
	 * самі, тому вони не можуть розійтися з формою.
	 *
	 * ЧОМУ ТУТ jQuery. Асиметрія, яку варто запам'ятати:
	 *
	 *   • наш нативний dispatchEvent(new Event('change')) jQuery ПОБАЧИТЬ,
	 *     бо він вішає слухачі через звичайний addEventListener;
	 *
	 *   • а от події, які Woo кидає через .trigger('reset_data'), —
	 *     синтетичні, вони існують лише всередині jQuery. Нативний
	 *     addEventListener('reset_data') не спрацює ніколи.
	 *
	 * Тому писати в форму можна чистим JS, а слухати її — тільки через jQuery.
	 */
	function initVariationSwatches() {
		const form = document.querySelector('.variations_form');

		if (!form) {
			return;
		}

		/**
		 * Перемальовує кнопки однієї групи за поточним станом її select.
		 *
		 * @param {HTMLElement} group Контейнер [data-swatches-for].
		 */
		function syncGroup(group) {
			const select = form.querySelector('#' + group.dataset.swatchesFor);

			if (!select) {
				return;
			}

			// Значення, які Woo лишив доступними для поточного вибору.
			const available = Array.from(select.options)
				.map(function (option) { return option.value; })
				.filter(Boolean);

			group.querySelectorAll('.swatches__item').forEach(function (button) {
				const value = button.dataset.value;
				const isActive = value === select.value;
				const isAvailable = available.includes(value);

				button.classList.toggle('swatches__item--active', isActive);
				button.classList.toggle('swatches__item--unavailable', !isAvailable);
				button.setAttribute('aria-pressed', String(isActive));
				button.disabled = !isAvailable;
			});
		}

		function syncAll() {
			form.querySelectorAll('[data-swatches-for]').forEach(syncGroup);
		}

		// Клік по кнопці → пишемо в select і повідомляємо про зміну.
		// Далі Woo робить усе сам, а ми лише перемальовуємо кнопки.
		form.addEventListener('click', function (event) {
			const button = event.target.closest('.swatches__item');

			if (!button || button.disabled) {
				return;
			}

			const group = button.closest('[data-swatches-for]');
			const select = form.querySelector('#' + group.dataset.swatchesFor);

			if (!select) {
				return;
			}

			// Повторний клік по обраному знімає вибір — так само, як «Clear».
			select.value = select.value === button.dataset.value ? '' : button.dataset.value;
			select.dispatchEvent(new Event('change', { bubbles: true }));
		});

		// Слухаємо форму через jQuery: інакше не побачимо ні перерахунок
		// доступних комбінацій, ні натискання «Clear».
		if (window.jQuery) {
			window.jQuery(form).on('change woocommerce_variation_has_changed reset_data', function () {
				// Woo переписує <option> у сусідніх селектах уже після події,
				// тому синхронізуємось наступним кадром.
				window.requestAnimationFrame(syncAll);
			});
		}

		syncAll();
	}

	/**
	 * Вкладки на сторінці товару.
	 *
	 * Зв'язок «кнопка → панель» береться з aria-controls: кнопка знає id
	 * своєї панелі. Ці ж атрибути роблять вкладки доступними для скрінрідера,
	 * тому додаткової розмітки для JS не потрібно.
	 */
	function initTabs() {
		const containers = document.querySelectorAll('[data-tabs]');

		containers.forEach(function (container) {
			const tabs = container.querySelectorAll('.product-tabs__tab');
			const panels = container.querySelectorAll('.product-tabs__panel');

			tabs.forEach(function (tab) {
				tab.addEventListener('click', function () {
					const targetId = tab.getAttribute('aria-controls');

					tabs.forEach(function (other) {
						const isCurrent = other === tab;

						other.classList.toggle('product-tabs__tab--active', isCurrent);
						other.setAttribute('aria-selected', String(isCurrent));
					});

					panels.forEach(function (panel) {
						const isCurrent = panel.id === targetId;

						panel.classList.toggle('product-tabs__panel--active', isCurrent);
						panel.hidden = !isCurrent;
					});
				});
			});
		});
	}

	/**
	 * Слайдери: hero на головній і стрічки товарів.
	 *
	 * Це не повноцінний слайдер-плагін: стрічка гортається силами CSS
	 * (overflow-x + scroll-snap), а JS лише перемотує її по кліку
	 * й підсвічує потрібну крапку. Менше коду — менше що ламати.
	 *
	 * Розмітка:
	 *   [data-slider]            — контейнер
	 *     [data-slider-track]    — стрічка, що прокручується
	 *     [data-slider-dots]     — крапки (не обов'язково)
	 *     [data-slider-prev/next]— стрілки (не обов'язково)
	 *
	 * Пошук іде через slider.querySelector, а не document.querySelector —
	 * тому на одній сторінці спокійно живуть кілька незалежних слайдерів.
	 */
	function initSliders() {
		const sliders = document.querySelectorAll('[data-slider]');

		sliders.forEach(function (slider) {
			const track = slider.querySelector('[data-slider-track]');

			if (!track) {
				return;
			}

			const dots = Array.from(slider.querySelectorAll('.slider-dots__dot'));
			const prev = slider.querySelector('[data-slider-prev]');
			const next = slider.querySelector('[data-slider-next]');

			function setActiveDot(index) {
				dots.forEach(function (dot, i) {
					dot.classList.toggle('slider-dots__dot--active', i === index);
				});
			}

			dots.forEach(function (dot, index) {
				dot.addEventListener('click', function () {
					// Уся довжина прокрутки, поділена на кількість крапок.
					const step = (track.scrollWidth - track.clientWidth) / (dots.length - 1 || 1);

					track.scrollTo({ left: step * index, behavior: 'smooth' });
					setActiveDot(index);
				});
			});

			// Стрілки гортають рівно на одну видиму ширину стрічки.
			if (prev) {
				prev.addEventListener('click', function () {
					track.scrollBy({ left: -track.clientWidth, behavior: 'smooth' });
				});
			}

			if (next) {
				next.addEventListener('click', function () {
					track.scrollBy({ left: track.clientWidth, behavior: 'smooth' });
				});
			}

			// Гортання пальцем теж має підсвічувати потрібну крапку.
			if (dots.length > 0) {
				track.addEventListener('scroll', function () {
					const maxScroll = track.scrollWidth - track.clientWidth;

					if (maxScroll <= 0) {
						setActiveDot(0);
						return;
					}

					setActiveDot(Math.round((track.scrollLeft / maxScroll) * (dots.length - 1)));
				}, { passive: true });
			}
		});
	}

	// Скрипт підключений з defer, тому DOM уже готовий на момент запуску.
	initBurger();
	initMobileSubmenu();
	initQuantity();
	initVariationSwatches();
	initTabs();
	initSliders();
})();
