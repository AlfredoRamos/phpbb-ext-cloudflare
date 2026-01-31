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
	'CAPTCHA_TURNSTILE_EXPLAIN' => 'Consulta las <a href="https://www.phpbb.com/customise/db/extension/cloudflare/faq" rel="external nofollow noreferrer noopener" target="_blank"><strong>Preguntas Frecuentes</strong></a> para obtener más información. Si requieres de ayuda, por favor visita la sección de <a href="https://www.phpbb.com/customise/db/extension/cloudflare/support" rel="external nofollow noreferrer noopener" target="_blank"><strong>Soporte</strong></a>.',
	'TURNSTILE_KEY' => 'Clave del sitio',
	'TURNSTILE_KEY_EXPLAIN' => 'La clave del sitio generada en Turnstile para tu dominio.',
	'TURNSTILE_SECRET' => 'Clave secreta',
	'TURNSTILE_SECRET_EXPLAIN' => 'La clave secreta generada en tu cuenta Turnstile.',
	'TURNSTILE_THEME' => 'Tema',
	'TURNSTILE_THEME_EXPLAIN' => 'El color del widget de Turnstile.',
	'TURNSTILE_THEME_AUTO' => 'Automático',
	'TURNSTILE_THEME_LIGHT' => 'Claro',
	'TURNSTILE_THEME_DARK' => 'Obscuro',
	'TURNSTILE_SIZE' => 'Tamaño',
	'TURNSTILE_SIZE_EXPLAIN' => 'El tamaño del widget de hCapcha.',
	'TURNSTILE_SIZE_NORMAL' => 'Normal',
	'TURNSTILE_SIZE_FLEXIBLE' => 'Flexible',
	'TURNSTILE_SIZE_COMPACT' => 'Compacto',
	'TURNSTILE_APPEARANCE' => 'Apariencia',
	'TURNSTILE_APPEARANCE_EXPLAIN' => 'La visibilidad del widget de Turnstile.',
	'TURNSTILE_APPEARANCE_ALWAYS' => 'Siempre',
	'TURNSTILE_NOT_AVAILABLE' => 'Para poder utilizar Turnstile, debes crear una cuenta en <a href="https://dash.cloudflare.com/?to=/:account/turnstile" rel="external nofollow noreferrer noopener" target="_blank">www.cloudflare.com</a>.',
	'TURNSTILE_INCORRECT' => 'La solución que proporcionaste es incorrecta.',
	'TURNSTILE_NOSCRIPT' => 'Por favor, habilita JavaScript en tu navegador web para cargar el desafío.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'Has superado el número máximo de intentos de inicio de sesión permitidos.<br>Además de tu nombre de usuario y contraseña, se utilizará Turnstile para autenticar tu sesión.',

	'CLOUDFLARE_REQUEST_EXCEPTION' => 'Error de solicitud de Cloudflare: %s',

	'ACP_CLOUDFLARE_TOGGLE_SECRET' => 'Alternar visibilidad de %s',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Valores inválidos para los campos: <samp>%s</samp>'
]);
