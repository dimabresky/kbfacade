(function () {
	'use strict';

	var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function qs(root, selector) {
		return (root || document).querySelector(selector);
	}

	function qsa(root, selector) {
		return Array.prototype.slice.call((root || document).querySelectorAll(selector));
	}

	function initHeader(header) {
		var burger = qs(header, '[data-kbf-burger]');
		var mobile = qs(header, '[data-kbf-mobile]');
		if (!burger || !mobile) {
			return;
		}

		burger.addEventListener('click', function () {
			var open = burger.getAttribute('aria-expanded') === 'true';
			burger.setAttribute('aria-expanded', open ? 'false' : 'true');
			if (open) {
				mobile.setAttribute('hidden', '');
			} else {
				mobile.removeAttribute('hidden');
			}
		});

		qsa(mobile, 'a').forEach(function (link) {
			link.addEventListener('click', function () {
				burger.setAttribute('aria-expanded', 'false');
				mobile.setAttribute('hidden', '');
			});
		});
	}

	function initModal() {
		var modal = qs(document, '[data-kbf-modal]');
		if (!modal) {
			return;
		}

		var lastFocus = null;

		function openModal() {
			lastFocus = document.activeElement;
			modal.removeAttribute('hidden');
			document.body.style.overflow = 'hidden';
			var focusable = qs(modal, 'input, button, textarea, select, a[href]');
			if (focusable) {
				focusable.focus();
			}
		}

		function closeModal() {
			modal.setAttribute('hidden', '');
			document.body.style.overflow = '';
			if (lastFocus && lastFocus.focus) {
				lastFocus.focus();
			}
		}

		qsa(document, '[data-kbf-open-modal]').forEach(function (btn) {
			btn.addEventListener('click', openModal);
		});
		qsa(modal, '[data-kbf-close-modal]').forEach(function (btn) {
			btn.addEventListener('click', closeModal);
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !modal.hasAttribute('hidden')) {
				closeModal();
			}
		});
	}

	function initCarousel(root) {
		var track = qs(root, '[data-kbf-carousel-track]');
		if (!track) {
			return;
		}

		var autoplay = root.getAttribute('data-autoplay') === 'yes' && !reducedMotion;
		var speed = parseInt(root.getAttribute('data-speed') || '5000', 10);
		var timer = null;
		var prev = qs(root, '[data-kbf-carousel-prev]');
		var next = qs(root, '[data-kbf-carousel-next]');

		function scrollByDir(dir) {
			var amount = Math.max(240, Math.floor(track.clientWidth * 0.8));
			track.scrollBy({ left: dir * amount, behavior: reducedMotion ? 'auto' : 'smooth' });
		}

		function updateNav() {
			var maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
			var overflow = maxScroll > 1;
			var atStart = track.scrollLeft <= 1;
			var atEnd = track.scrollLeft >= maxScroll - 1;

			if (prev) {
				if (!overflow || atStart) {
					prev.setAttribute('hidden', '');
				} else {
					prev.removeAttribute('hidden');
				}
			}
			if (next) {
				if (!overflow || atEnd) {
					next.setAttribute('hidden', '');
				} else {
					next.removeAttribute('hidden');
				}
			}
		}

		if (prev) {
			prev.addEventListener('click', function () {
				scrollByDir(-1);
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				scrollByDir(1);
			});
		}

		var startX = 0;
		track.addEventListener(
			'touchstart',
			function (event) {
				startX = event.changedTouches[0].clientX;
			},
			{ passive: true }
		);
		track.addEventListener(
			'touchend',
			function (event) {
				var dx = event.changedTouches[0].clientX - startX;
				if (Math.abs(dx) > 40) {
					scrollByDir(dx < 0 ? 1 : -1);
				}
			},
			{ passive: true }
		);

		track.addEventListener('scroll', updateNav, { passive: true });
		window.addEventListener('resize', updateNav);

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function start() {
			stop();
			if (!autoplay || speed <= 0) {
				return;
			}
			timer = window.setInterval(function () {
				if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 8) {
					track.scrollTo({ left: 0, behavior: reducedMotion ? 'auto' : 'smooth' });
				} else {
					scrollByDir(1);
				}
			}, speed);
		}

		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', start);
		root.addEventListener('focusin', stop);
		root.addEventListener('focusout', start);
		updateNav();
		window.requestAnimationFrame(updateNav);
		start();
	}

	function initCatalog(root) {
		var button = qs(root, '[data-kbf-catalog-more]');
		if (!button) {
			return;
		}
		button.addEventListener('click', function () {
			qsa(root, '.kbf-catalog__item[hidden]').forEach(function (item) {
				item.removeAttribute('hidden');
				item.classList.remove('is-collapsed');
			});
			button.setAttribute('hidden', '');
		});
	}

	function initGallery(root) {
		var lightbox = qs(root, '[data-kbf-lightbox]');
		var image = qs(root, '[data-kbf-lightbox-image]');
		if (!lightbox || !image) {
			return;
		}

		function close() {
			lightbox.setAttribute('hidden', '');
			image.setAttribute('src', '');
			document.body.style.overflow = '';
		}

		qsa(root, '[data-kbf-lightbox-src]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				image.setAttribute('src', btn.getAttribute('data-kbf-lightbox-src'));
				image.setAttribute('alt', btn.getAttribute('aria-label') || '');
				lightbox.removeAttribute('hidden');
				document.body.style.overflow = 'hidden';
			});
		});

		var closeBtn = qs(root, '[data-kbf-lightbox-close]');
		if (closeBtn) {
			closeBtn.addEventListener('click', close);
		}
		lightbox.addEventListener('click', function (event) {
			if (event.target === lightbox) {
				close();
			}
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !lightbox.hasAttribute('hidden')) {
				close();
			}
		});
	}

	function initForms() {
		var endpoint = (window.kbfacadeElementor && window.kbfacadeElementor.restUrl) || '';
		var nonce = (window.kbfacadeElementor && window.kbfacadeElementor.nonce) || '';
		var i18n = (window.kbfacadeElementor && window.kbfacadeElementor.i18n) || {};

		qsa(document, '[data-kbf-form]').forEach(function (form) {
			form.addEventListener('submit', function (event) {
				event.preventDefault();
				var message = qs(form, '[data-kbf-form-message]');
				var submit = qs(form, 'button[type="submit"]');
				var original = submit ? submit.textContent : '';

				if (message) {
					message.textContent = '';
					message.classList.remove('is-error');
				}
				if (submit) {
					submit.disabled = true;
					submit.textContent = i18n.sending || 'Отправка…';
				}

				var data = {
					form_type: form.getAttribute('data-form-type') || 'modal',
					company: (form.elements.company && form.elements.company.value) || '',
					name: (form.elements.name && form.elements.name.value) || '',
					phone: (form.elements.phone && form.elements.phone.value) || '',
					email: (form.elements.email && form.elements.email.value) || '',
					consent: !!(form.elements.consent && form.elements.consent.checked),
					website: (form.elements.website && form.elements.website.value) || '',
					page_url: window.location.href,
				};

				fetch(endpoint, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': nonce,
					},
					body: JSON.stringify(data),
				})
					.then(function (response) {
						return response.json().then(function (payload) {
							return { ok: response.ok, payload: payload };
						});
					})
					.then(function (result) {
						if (!message) {
							return;
						}
						if (result.ok && result.payload && result.payload.success) {
							message.textContent = result.payload.message;
							form.reset();
						} else {
							message.classList.add('is-error');
							message.textContent =
								(result.payload && (result.payload.message || (result.payload.data && result.payload.data.message))) ||
								i18n.error ||
								'Ошибка отправки.';
						}
					})
					.catch(function () {
						if (message) {
							message.classList.add('is-error');
							message.textContent = i18n.error || 'Ошибка отправки.';
						}
					})
					.finally(function () {
						if (submit) {
							submit.disabled = false;
							submit.textContent = original;
						}
					});
			});
		});
	}

	function boot() {
		qsa(document, '[data-kbf-header]').forEach(initHeader);
		qsa(document, '[data-kbf-carousel]').forEach(initCarousel);
		qsa(document, '[data-kbf-catalog]').forEach(initCatalog);
		qsa(document, '[data-kbf-gallery]').forEach(initGallery);
		initModal();
		initForms();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Elementor preview / SPA-like re-init.
	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', boot);
	}
})();
