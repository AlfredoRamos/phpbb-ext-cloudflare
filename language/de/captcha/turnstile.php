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
	'CAPTCHA_TURNSTILE_EXPLAIN' => 'Weitere Informationen finden Sie in der <a href="https://www.phpbb.com/customise/db/extension/cloudflare/faq" rel="external nofollow noreferrer noopener" target="_blank"><strong>FAQ</strong></a>. Wenn Sie Hilfe benötigen, besuchen Sie bitte den <a href="https://www.phpbb.com/customise/db/extension/cloudflare/support" rel="external nofollow noreferrer noopener" target="_blank"><strong>Support</strong></a>-Bereich.',
	'TURNSTILE_KEY' => 'Sitekey',
	'TURNSTILE_KEY_EXPLAIN' => 'Der Sitekey, der auf Turnstile für Ihre Domain generiert wurde.',
	'TURNSTILE_SECRET' => 'Geheimer Schlüssel',
	'TURNSTILE_SECRET_EXPLAIN' => 'Der in Ihrem Turnstile‑Konto generierte geheime Schlüssel.',
	'TURNSTILE_THEME' => 'Thema',
	'TURNSTILE_THEME_EXPLAIN' => 'Das Farbschema des Turnstile-Widgets.',
	'TURNSTILE_THEME_LIGHT' => 'Hell',
	'TURNSTILE_THEME_DARK' => 'Dunkel',
	'TURNSTILE_SIZE' => 'Größe',
	'TURNSTILE_SIZE_EXPLAIN' => 'Die Größe des Turnstile-Widgets.',
	'TURNSTILE_SIZE_NORMAL' => 'Normal',
	'TURNSTILE_SIZE_COMPACT' => 'Kompakt',
	'TURNSTILE_NOT_AVAILABLE' => 'Um Turnstile nutzen zu können, müssen Sie ein Konto bei <a href="https://dash.cloudflare.com/?to=/:account/turnstile" rel="external nofollow noreferrer noopener" target="_blank">www.cloudflare.com</a> erstellen.',
	'TURNSTILE_INCORRECT' => 'Die von Ihnen eingegebene Lösung war falsch.',
	'TURNSTILE_NOSCRIPT' => 'Bitte aktivieren Sie JavaScript in Ihrem Browser, um die Challenge zu laden.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'Sie haben die maximal zulässige Anzahl an Anmeldeversuchen überschritten.<br>Zusätzlich zu Ihrem Benutzernamen und Passwort wird Turnstile zur Authentifizierung Ihrer Sitzung verwendet.',
	'TURNSTILE_REQUEST_EXCEPTION' => 'Turnstile Anforderungsfehler: %s',

	'ACP_CLOUDFLARE_TOGGLE_SECRET' => '%s umschalten',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Ungültige Werte für Felder: <samp>%s</samp>'
]);
