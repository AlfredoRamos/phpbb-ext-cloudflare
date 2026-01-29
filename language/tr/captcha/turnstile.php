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
	'CAPTCHA_TURNSTILE_EXPLAIN' => 'Daha fazla bilgi için <a href="https://www.phpbb.com/customise/db/extension/cloudflare/faq" rel="external nofollow noreferrer noopener" target="_blank"><strong>FAQ</strong></a>\'e bakınız. Yardıma ihtiyacınız varsa lütfen <a href="https://www.phpbb.com/customise/db/extension/cloudflare/support" rel="external nofollow noreferrer noopener" target="_blank"><strong>Destek</strong></a> bölümünü ziyaret edin.',
	'TURNSTILE_KEY' => 'Site anahtarı',
	'TURNSTILE_KEY_EXPLAIN' => 'Alan adınız için Turnstile\'da oluşturulan site anahtarı.',
	'TURNSTILE_SECRET' => 'Gizli anahtar',
	'TURNSTILE_SECRET_EXPLAIN' => 'Turnstile hesabınızda oluşturulan gizli anahtar.',
	'TURNSTILE_THEME' => 'Tema',
	'TURNSTILE_THEME_EXPLAIN' => 'Turnstile widget\'ının renk teması.',
	'TURNSTILE_THEME_LIGHT' => 'Açık',
	'TURNSTILE_THEME_DARK' => 'Koyu',
	'TURNSTILE_SIZE' => 'Boyut',
	'TURNSTILE_SIZE_EXPLAIN' => 'Turnstile widget\'ının boyutu.',
	'TURNSTILE_SIZE_NORMAL' => 'Normal',
	'TURNSTILE_SIZE_COMPACT' => 'Kompakt',
	'TURNSTILE_NOT_AVAILABLE' => 'Turnstile kullanmak için <a href="https://dash.cloudflare.com/?to=/:account/turnstile" rel="external nofollow noreferrer noopener" target="_blank">www.cloudflare.com</a> sitesinde bir hesap oluşturmalısınız.',
	'TURNSTILE_INCORRECT' => 'Sağladığınız çözüm yanlıştı.',
	'TURNSTILE_NOSCRIPT' => 'Lütfen meydan okumayı yüklemek için tarayıcınızda JavaScript\'i etkinleştirin.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'İzin verilen maksimum giriş denemesi sayısını aştınız.<br>Kullanıcı adınıza ve şifrenize ek olarak, oturumunuzun kimliğini doğrulamak için Turnstile kullanılacaktır.',
	'TURNSTILE_REQUEST_EXCEPTION' => 'Turnstile istek hatası: %s',

	'ACP_TURNSTILE_TOGGLE_SECRET' => 'Aç/kapat %s',
	'ACP_TURNSTILE_VALIDATE_INVALID_FIELDS' => 'Şu alanlar için geçersiz değerler: <samp>%s</samp>'
]);
