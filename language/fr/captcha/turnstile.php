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
	'CAPTCHA_TURNSTILE_EXPLAIN' => 'Consultez la <a href="https://www.phpbb.com/customise/db/extension/cloudflare/faq" rel="external nofollow noreferrer noopener" title="S’ouvre dans un nouvel onglet" target="_blank"><strong>FAQ</strong></a> pour plus d’informations. Si vous avez besoin d’aide, veuillez vous rendre dans la section de <a href="https://www.phpbb.com/customise/db/extension/cloudflare/support" rel="external nofollow noreferrer noopener" title="S’ouvre dans un nouvel onglet" target="_blank"><strong>support</strong></a>.',
	'TURNSTILE_KEY' => 'Clé de site',
	'TURNSTILE_KEY_EXPLAIN' => 'Clé de site générée sur Turnstile pour votre domaine.',
	'TURNSTILE_SECRET' => 'Clé secrète',
	'TURNSTILE_SECRET_EXPLAIN' => 'Clé secrète générée sur votre compte Turnstile.',
	'TURNSTILE_THEME' => 'Thème',
	'TURNSTILE_THEME_EXPLAIN' => 'Thème de couleur du widget Turnstile.',
	'TURNSTILE_THEME_LIGHT' => 'Clair',
	'TURNSTILE_THEME_DARK' => 'Sombre',
	'TURNSTILE_SIZE' => 'Taille',
	'TURNSTILE_SIZE_EXPLAIN' => 'Taille du widget Turnstile.',
	'TURNSTILE_SIZE_NORMAL' => 'Normale',
	'TURNSTILE_SIZE_COMPACT' => 'Compacte',
	'TURNSTILE_NOT_AVAILABLE' => 'Pour utiliser Turnstile, vous devez créer un compte sur <a href="https://www.cloudflare.com/" rel="external nofollow noreferrer noopener" title="S’ouvre dans un nouvel onglet" target="_blank">www.cloudflare.com</a>.',
	'TURNSTILE_INCORRECT' => 'La solution que vous avez indiquée est incorrecte.',
	'TURNSTILE_NOSCRIPT' => 'Veuillez activer JavaScript dans votre navigateur pour charger le test.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'Vous avez dépassé le nombre maximum de tentatives de connexion autorisées.<br>En plus de votre nom d’utilisateur et de votre mot de passe, Turnstile sera utilisé pour authentifier votre session.',
	'TURNSTILE_REQUEST_EXCEPTION' => 'Erreur de requête Turnstile : %s',

	'ACP_CLOUDFLARE_TOGGLE_SECRET' => 'Afficher/Masquer la %s',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Valeurs invalides pour les champs : <samp>%s</samp>'
]);
