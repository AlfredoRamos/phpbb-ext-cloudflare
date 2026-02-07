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
	'CAPTCHA_TURNSTILE_EXPLAIN' => '<p>Consult the <a href="%1$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>FAQ</strong></a> for more information. If you require assistance, please visit the <a href="%2$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Support</strong></a> section.</p><p>If you like or found this extension useful and want to show some appreciation, you can consider supporting its development by <a href="%3$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>giving a donation</strong></a>.</p>',
	'TURNSTILE_KEY' => 'Site key',
	'TURNSTILE_KEY_EXPLAIN' => 'The site key generated on Turnstile for your domain.',
	'TURNSTILE_SECRET' => 'Secret key',
	'TURNSTILE_SECRET_EXPLAIN' => 'The secret key generated on your Turnstile account.',
	'TURNSTILE_THEME' => 'Theme',
	'TURNSTILE_THEME_EXPLAIN' => 'The color theme of the Turnstile widget.',
	'TURNSTILE_THEME_AUTO' => 'Auto',
	'TURNSTILE_THEME_LIGHT' => 'Light',
	'TURNSTILE_THEME_DARK' => 'Dark',
	'TURNSTILE_SIZE' => 'Size',
	'TURNSTILE_SIZE_EXPLAIN' => 'The size of the Turnstile widget.',
	'TURNSTILE_SIZE_NORMAL' => 'Normal',
	'TURNSTILE_SIZE_FLEXIBLE' => 'Flexible',
	'TURNSTILE_SIZE_COMPACT' => 'Compact',
	'TURNSTILE_APPEARANCE' => 'Appearance',
	'TURNSTILE_APPEARANCE_EXPLAIN' => 'The visibility of the Turnstile widget.',
	'TURNSTILE_APPEARANCE_ALWAYS' => 'Always',
	'TURNSTILE_APPEARANCE_INTERACTION_ONLY' => 'Invisible',
	'TURNSTILE_NOT_AVAILABLE' => 'In order to use Turnstile, you must create an account on <a href="https://dash.cloudflare.com/?to=/:account/turnstile" rel="external nofollow noreferrer noopener" target="_blank">www.cloudflare.com</a>.',
	'TURNSTILE_INCORRECT' => 'The solution you provided was incorrect.',
	'TURNSTILE_NOSCRIPT' => 'Please enable JavaScript in your browser to load the challenge.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'You have exceeded the maximum number of login attempts allowed.<br>In addition to your username and password, Turnstile will be used to authenticate your session.',

	'CLOUDFLARE_REQUEST_EXCEPTION' => 'Cloudflare request error: %s'
]);
