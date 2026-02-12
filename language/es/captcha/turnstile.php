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
	'CAPTCHA_TURNSTILE_EXPLAIN' => '<p>Consulte las <a href="%1$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Preguntas Frecuentes</strong></a> para obtener más información. Si requiere de ayuda, por favor visite la sección de <a href="%2$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Soporte</strong></a>.</p><p>Si le gustó o encontró útil esta extensión y quiere mostrar un gesto de agradecimiento, puede considerar contribuir a su desarrollo realizando una <a href="%3$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>donación</strong></a>.</p>',
	'TURNSTILE_KEY' => 'Clave del sitio',
	'TURNSTILE_KEY_EXPLAIN' => 'La clave del sitio generada en Turnstile para su dominio.',
	'TURNSTILE_SECRET' => 'Clave secreta',
	'TURNSTILE_SECRET_EXPLAIN' => 'La clave secreta generada en su cuenta Turnstile.',
	'TURNSTILE_THEME' => 'Tema',
	'TURNSTILE_THEME_EXPLAIN' => 'El color del widget de Turnstile.',
	'TURNSTILE_THEME_AUTO' => 'Automático',
	'TURNSTILE_THEME_LIGHT' => 'Claro',
	'TURNSTILE_THEME_DARK' => 'Obscuro',
	'TURNSTILE_SIZE' => 'Tamaño',
	'TURNSTILE_SIZE_EXPLAIN' => 'El tamaño del widget de Turnstile.',
	'TURNSTILE_SIZE_NORMAL' => 'Normal',
	'TURNSTILE_SIZE_FLEXIBLE' => 'Flexible',
	'TURNSTILE_SIZE_COMPACT' => 'Compacto',
	'TURNSTILE_APPEARANCE' => 'Apariencia',
	'TURNSTILE_APPEARANCE_EXPLAIN' => 'La visibilidad del widget de Turnstile.',
	'TURNSTILE_APPEARANCE_ALWAYS' => 'Siempre',
	'TURNSTILE_APPEARANCE_INTERACTION_ONLY' => 'Invisible',
	'TURNSTILE_NOT_AVAILABLE' => 'Para poder utilizar Turnstile, debe crear una cuenta en <a href="https://dash.cloudflare.com/?to=/:account/turnstile" rel="external nofollow noreferrer noopener" target="_blank">www.cloudflare.com</a>.',
	'TURNSTILE_INCORRECT' => 'La solución que proporcionó es incorrecta.',
	'TURNSTILE_NOSCRIPT' => 'Por favor, habilite JavaScript en su navegador web para cargar el desafío.',
	'TURNSTILE_LOGIN_ERROR_ATTEMPTS' => 'Ha superado el número máximo de intentos de inicio de sesión permitidos.<br>Además de su nombre de usuario y contraseña, se utilizará Turnstile para autenticar su sesión.',

	'CLOUDFLARE_REQUEST_EXCEPTION' => 'Error de solicitud de Cloudflare: %s'
]);
