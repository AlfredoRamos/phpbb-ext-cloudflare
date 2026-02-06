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

	document.body.querySelectorAll('.cf-btn-toggle')?.forEach((elem) => {
		elem?.addEventListener('click', (e) => {
			const toggle = e.target.closest('.cf-btn-toggle');
			const container = toggle.parentElement;

			if (!toggle || !container) {
				return;
			}

			const field = container?.querySelector('.cf-field-secret');
			const icon = toggle?.querySelector('.icon');

			if (!field || !icon) {
				return;
			}

			const isHidden = field.getAttribute('type').trim() === 'password';
			field.setAttribute('type', isHidden ? 'text' : 'password');
			icon.classList.toggle('fa-eye-slash', isHidden);
			icon.classList.toggle('fa-eye', !isHidden);
		});
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

	const purgeCacheForm = document.body.querySelector(
		'#cloudflare_purge_cache_form',
	);

	const fields = {
		type: purgeCacheForm?.querySelector(
			'[name="cloudflare_purge_cache_type"]',
		),
		value: purgeCacheForm?.querySelector(
			'[name="cloudflare_purge_cache_value"]',
		),
	};
	const button = purgeCacheForm?.querySelector('.cf-btn');
	const valueContainer = purgeCacheForm?.querySelector(
		'.cloudflare-value-container',
	);

	fields?.type?.addEventListener('change', (e) => {
		const val = e.target.value ?? '';

		switch (val) {
			case 'purge_everything':
				fields.value.value = null;
				valueContainer.style.display = 'none';
				valueContainer.open = false;
				break;

			case 'hosts':
				fields.value.value =
					window?.cloudflareCfg?.config?.baseURL?.replace(
						/https?:\/\//,
						'',
					);
				break;
		}

		if (val !== 'purge_everything') {
			fields.value.value = fields.value.value.trim();
			valueContainer.style.display = 'block';
			valueContainer.open = true;
		}
	});

	const darkWrapper = document.body.querySelector('#darkenwrapper');

	purgeCacheForm?.addEventListener('submit', (e) => {
		e.preventDefault();

		fields?.type?.setAttribute('disabled', '');
		fields?.value?.setAttribute('disabled', '');
		button?.setAttribute('disabled', '');

		fetch(purgeCacheForm.getAttribute('action').trim(), {
			method: purgeCacheForm.getAttribute('method'),
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
				'Cache-Control': 'no-cache',
			},
			body: JSON.stringify({
				type: fields?.type?.value ?? '',
				value: fields?.value?.value ?? '',
			}),
		})
			.then((r) => {
				const json = r.json();

				if (!r.ok) {
					return json.then((e) => {
						return Promise.reject({
							status: r.status,
							body: e,
							headers: {
								'Content-Type': 'application/json',
							},
						});
					});
				}

				return json;
			})
			.then((r) => {
				if (!r.success) {
					window.phpbb.alert(
						darkWrapper?.getAttribute('data-ajax-error-title'),
						darkWrapper?.getAttribute('data-ajax-error-text'),
					);
					return;
				}

				window.phpbb.alert(
					window.cloudflareCfg?.lang?.purgeCacheSuccessTitle,
					window.cloudflareCfg?.lang?.purgeCacheSuccessBody,
				);
			})
			.catch((e) => {
				if (e?.body?.errors?.length <= 0) {
					window.phpbb.alert(
						darkWrapper?.getAttribute('data-ajax-error-title'),
						darkWrapper?.getAttribute('data-ajax-error-text'),
					);
					return;
				}

				let message = '';

				e?.body?.errors?.forEach((err, idx) => {
					if (!err) {
						return;
					}

					message += err?.message ?? '';

					if (idx < e?.errors?.length - 1) {
						message += '<br>';
					}
				});

				message = message.trim();

				if (message.length <= 0) {
					window.phpbb.alert(
						darkWrapper?.getAttribute('data-ajax-error-title'),
						darkWrapper?.getAttribute('data-ajax-error-text'),
					);
					return;
				}

				window.phpbb.alert(
					darkWrapper?.getAttribute('data-ajax-error-title'),
					message,
				);
			})
			.finally(() => {
				fields?.type?.removeAttribute('disabled');
				fields?.value?.removeAttribute('disabled');
			});
	});

	document.body.querySelectorAll('.cf-rules-sync')?.forEach((elem) => {
		elem?.addEventListener('click', (e) => {
			e.preventDefault();

			const button = e.target.closest('.cf-rules-sync');
			const endpoint = button?.getAttribute('data-url') ?? '';

			if (!button || !endpoint) {
				return;
			}

			e.target.setAttribute('disabled', '');
			const container = button.parentElement;
			const fields = {
				rulesetID: container.querySelector('input[type="text"]'),
			};

			fetch(endpoint, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
					'Cache-Control': 'no-cache',
				},
			})
				.then((r) => {
					console.log('then json: ', r);
					const json = r.json();

					if (!r.ok) {
						return json.then((e) => {
							return Promise.reject({
								status: r.status,
								body: e,
								headers: {
									'Content-Type': 'application/json',
								},
							});
						});
					}

					return json;
				})
				.then((r) => {
					console.log('then: ', r); // TODO: Delete
					if (!r.success) {
						window.phpbb.alert(
							darkWrapper?.getAttribute('data-ajax-error-title'),
							darkWrapper?.getAttribute('data-ajax-error-text'),
						);
						return;
					}

					fields.rulesetID.value = r?.result?.id;

					window.phpbb.alert(
						window.cloudflareCfg?.lang?.rulesetRulesSuccessTitle,
						window.cloudflareCfg?.lang?.rulesetRulesSuccessBody,
					);
				})
				.catch((e) => {
					console.log('then: ', e); // TODO: Delete
					if (e?.body?.errors?.length <= 0) {
						window.phpbb.alert(
							darkWrapper?.getAttribute('data-ajax-error-title'),
							darkWrapper?.getAttribute('data-ajax-error-text'),
						);
						return;
					}

					let message = '';

					e?.body?.errors?.forEach((err, idx) => {
						if (!err) {
							return;
						}

						message += err?.message ?? '';

						if (idx < e?.errors?.length - 1) {
							message += '<br>';
						}
					});

					message = message.trim();

					if (message.length <= 0) {
						window.phpbb.alert(
							darkWrapper?.getAttribute('data-ajax-error-title'),
							darkWrapper?.getAttribute('data-ajax-error-text'),
						);
						return;
					}

					window.phpbb.alert(
						darkWrapper?.getAttribute('data-ajax-error-title'),
						message,
					);
				})
				.finally(() => {
					fields?.rulesetID?.removeAttribute('disabled');
				});
		});
	});

	window.phpbb.resizeTextArea(
		jQuery('[name="cloudflare_purge_cache_value"]'),
	);
})();
