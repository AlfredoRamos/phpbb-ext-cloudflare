<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB')) {
	exit;
}

/**
 * @ignore
 */
if (empty($lang) || !is_array($lang)) {
	$lang = [];
}

$lang = array_merge($lang, [
	'ACP_CLOUDFLARE_SETTINGS_EXPLAIN' => '<p>Aquí puedes configurar los datos del API de Cloudflare. Consulta las <a href="%1$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Preguntas Frecuentes</strong></a> para obtener más información. Si requieres de ayuda, por favor visita la sección de <a href="%2$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Soporte</strong></a> section.</p><p>Si te gustó o encontraste útil esta extensión y quieres mostrar un gesto de agradecimiento, puedes considerar contribuir a su desarrollo realizando una <a href="%3$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>donación</strong></a>.</p>',

	'ACP_CLOUDFLARE_API_TOKEN' => 'Token de API',
	'ACP_CLOUDFLARE_API_TOKEN_EXPLAIN' => 'El token de la API de Cloudflare con los permisos <code>Zona:WAF de zona:Editar</code>, <code>Zona:Cache Rules:Editar</code> y <code>Zona:Purga de caché:Purgar</code>.',
	'ACP_CLOUDFLARE_ZONE_ID' => 'ID de Zona',
	'ACP_CLOUDFLARE_ZONE_ID_EXPLAIN' => 'El identificador de tu dominio en Cloudflare.',
	'ACP_CLOUDFLARE_FIREWALL_RULESET_ID' => 'ID del grupo de reglas de firewall',
	'ACP_CLOUDFLARE_FIREWALL_RULESET_ID_EXPLAIN' => 'El ID del grupo de reglas que contiene las reglas personalizadas del firewall. Deja en blanco para buscarlo o generarlo según sea necesario al dar click en el botón <samp>%s</samp>.',
	'ACP_CLOUDFLARE_CACHE_RULESET_ID' => 'ID del grupo de reglas de caché',
	'ACP_CLOUDFLARE_CACHE_RULESET_ID_EXPLAIN' => 'El ID del grupo de reglas que contiene las reglas personalizadas de caché. Deja en blanco para buscarlo o generarlo según sea necesario al dar click en el botón <samp>%s</samp>.',

	'ACP_CLOUDFLARE_TOGGLE_SECRET' => 'Alternar visibilidad de %s',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Valores inválido para los campos: <samp>%s</samp>',

	'CLOUDFLARE_PURGE_CACHE' => 'Purgar la caché de Cloudflare',
	'CLOUDFLARE_PURGE_CACHE_EXPLAIN' => 'Purgar la caché de Cloudflare por tipo. Toma en cuenta que Cloudflare impone un límite de peticiones por segundo.',
	'CLOUDFLARE_PURGE_CACHE_TYPE_PURGE_EVERYTHING' => 'Purgar todo',
	'CLOUDFLARE_PURGE_CACHE_TYPE_HOSTS' => 'Nombres de host',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_TITLE' => 'Caché purgada',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_BODY' => 'La caché de Cloudflare se ha purgado correctamente.',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_TITLE' => 'Reglas del grupo de reglas actualizadas',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_BODY' => 'Las reglas del grupo de reglas de Cloudflare han sido actualizadas correctamente.',

	'TURNSTILE_FORCE_LOGIN' => 'Forzar medidas contra el spam en inicios de sesión',
	'TURNSTILE_FORCE_LOGIN_EXPLAIN' => 'Requiere que los usuarios siempre pasen la tarea anti-spam para ayudar a prevenir inicios de sesión automatizados.'
]);
