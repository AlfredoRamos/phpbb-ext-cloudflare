/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

(() => {
	'use strict';

	const debounce = (callback, wait) => {
		let timeout = null;

		return (...args) => {
			clearTimeout(timeout);
			timeout = setTimeout(() => callback?.apply(this, args), wait);
		};
	};

	const widget = document.body.querySelector('#cf-turnstile-preview');
	const loadPreview = debounce(() => {
		if (!widget) {
			return;
		}

		widget.innerHTML = '';
		window.turnstile.render(widget, {
			sitekey: widget.getAttribute('data-sitekey'),
			theme: widget.getAttribute('data-theme'),
			size: widget.getAttribute('data-size'),
			appearance: widget.getAttribute('data-appearance'),
		});
	}, 250);

	document.body
		.querySelector('.toggle-cloudflare-secret')
		?.addEventListener('click', (e) => {
			const toggle = e.target.closest('.toggle-cloudflare-secret');

			if (!toggle) {
				return;
			}

			const field = document.body.querySelector('.cloudflare-secret');
			const icon = toggle?.querySelector('.icon');

			if (!field || !icon) {
				return;
			}

			const isHidden = field.getAttribute('type').trim() === 'password';
			field.setAttribute('type', isHidden ? 'text' : 'password');
			icon.classList.toggle('fa-eye-slash', isHidden);
			icon.classList.toggle('fa-eye', !isHidden);
		});

	document.body
		.querySelectorAll(
			'#turnstile-theme,#turnstile-size,#turnstile-appearance',
		)
		?.forEach((elem) => {
			if (!widget) {
				return;
			}

			elem?.addEventListener('change', (e) => {
				const attr = (e.target?.id ?? '')?.replace('turnstile', 'data');
				const value = e.target.value;

				if (!widget?.hasAttribute(attr) || !value) {
					return;
				}

				widget.setAttribute(attr, value);
				loadPreview();
			});
		});
})();
