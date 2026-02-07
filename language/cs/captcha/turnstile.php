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
	'CAPTCHA_TURNSTILE_EXPLAIN' => 'Podrobnosti najdete v <a href="https://www.phpbb.com/customise/db/extension/cloudflare/faq" rel="external nofollow noreferrer noopener" target="_blank"><strong>často kladených otázkách</strong></a>. Pokud potebujete pomoc, navštivte sekci <a href="https://www.phpbb.com/customise/db/extension/cloudflare/support" rel="external nofollow noreferrer noopener" target="_blank"><strong>Support</strong></a>.',
	'TURNSTILE_KEY' => 'Klíč webu',
	'TURNSTILE_KEY_EXPLAIN' => 'Klíč vygenerovaný od Turnstile pro vaši doménu.',
	'TURNSTILE_SECRET' => 'Tajný klíč',
	'TURNSTILE_SECRET_EXPLAIN' => 'Tajný klíč vygenerovaný pro váš účet Turnstile.',
	'TURNSTILE_THEME' => 'Téma vzhledu',
	'TURNSTILE_THEME_EXPLAIN' => 'Barva vzhledu widgetu Turnstile.',
	'TURNSTILE_THEME_AUTO' => 'Auto',
	'TURNSTILE_THEME_LIGHT' => 'Světlý',
	'TURNSTILE_THEME_DARK' => 'Tmavý',
	'TURNSTILE_SIZE' => 'Velikost',
	'TURNSTILE_SIZE_EXPLAIN' => 'Velikost widgetu Turnstile.',
	'TURNSTILE_SIZE_NORMAL' => 'Normální',
	'TURNSTILE_SIZE_FLEXIBLE' => 'Flexible',
	'TURNSTILE_SIZE_COMPACT' => 'Kompaktní',
	'TURNSTILE_APPEARANCE' => 'Appearance',
	'TURNSTILE_APPEARANCE_EXPLAIN' => 'The visibility of the Turnstile widget.',
	'TURNSTILE_APPEARANCE_ALWAYS' => 'Always',
	'TURNSTILE_APPEARANCE_INTERACTION_ONLY' => 'Invisible',
	'TURNSTILE_NOT_AVAILABLE' => 'Pro používání služby Turnstile si nejprve vytvořte účet na <a href="https://dash.cloudflare.com/?to=/:account/turnstile" rel="external nofollow noreferrer noopener" target="_blank">www.cloudflare.com</a>.',
	'TURNSTILE_INCORRECT' => 'Zadali jste nesprávné řešení.',
	'TURNSTILE_NOSCRIPT' => 'Povolte ve svém prohlížeči JavaScript pro načtení Turnstile.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'Překročili jste maximální počet pokusů o přihlášení.<br>Pro ověření vaší relace vyplňte kromě svého uživatelského jména a hesla také Turnstile.',

	'CLOUDFLARE_REQUEST_EXCEPTION' => 'Chyba požadavku Cloudflare: %s'
]);
