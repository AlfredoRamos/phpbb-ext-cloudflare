<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * @ignore
 */
if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'CAPTCHA_TURNSTILE' => 'Cloudflare Turnstile',
	'CAPTCHA_TURNSTILE_EXPLAIN' => 'Посмотрите <a href="https://www.phpbb.com/customise/db/extension/cloudflare/faq" rel="external nofollow noreferrer noopener" target="_blank"><strong>FAQ</strong></a> для более подробной информации. Если вам необходима помощь, пожалуйста, перейдите в раздел <a href="https://www.phpbb.com/customise/db/extension/cloudflare/support" rel="external nofollow noreferrer noopener" target="_blank"><strong>Поддержка</strong></a>.',
	'TURNSTILE_KEY' => 'Ключ сайта',
	'TURNSTILE_KEY_EXPLAIN' => 'Ключ сайта сгенерирован на Turnstile для вашего домена.',
	'TURNSTILE_SECRET' => 'Секретный ключ',
	'TURNSTILE_SECRET_EXPLAIN' => 'Секретный ключ, созданный на вашем аккаунте Turnstile.',
	'TURNSTILE_THEME' => 'Тема',
	'TURNSTILE_THEME_EXPLAIN' => 'Цветовая тема виджета Turnstile.',
	'TURNSTILE_THEME_LIGHT' => 'Светлая',
	'TURNSTILE_THEME_DARK' => 'Тёмная',
	'TURNSTILE_SIZE' => 'Размер',
	'TURNSTILE_SIZE_EXPLAIN' => 'Размер виджета Turnstile.',
	'TURNSTILE_SIZE_NORMAL' => 'Обычный',
	'TURNSTILE_SIZE_COMPACT' => 'Компактный',
	'TURNSTILE_NOT_AVAILABLE' => 'Чтобы использовать Turnstile, необходимо создать учетную запись на сайте <a href="https://dash.cloudflare.com/?to=/:account/turnstile" rel="external nofollow noreferrer noopener" target="_blank">www.cloudflare.com</a>.',
	'TURNSTILE_INCORRECT' => 'Вы указали неправильное решение.',
	'TURNSTILE_NOSCRIPT' => 'Для загрузки включите JavaScript в вашем браузере.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'Вы превысили максимально допустимое количество попыток входа.<br>В дополнение к вашему логину и паролю будет использоваться Turnstile.',

	'CLOUDFLARE_REQUEST_EXCEPTION' => 'Ошибка запроса Cloudflare: %s',

	'ACP_CLOUDFLARE_TOGGLE_SECRET' => 'Переключатель %s',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Недопустимые значения для полей: <samp>%s</samp>'
]);
