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
	'ACP_CLOUDFLARE_SETTINGS_EXPLAIN' => '<p>Aquí puede configurar los datos del API de Cloudflare. Consulte las <a href="%1$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Preguntas Frecuentes</strong></a> para obtener más información. Si requiere de ayuda, por favor visite la sección de <a href="%2$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Soporte</strong></a> section.</p><p>Si le gustó o encontró útil esta extensión y quiere mostrar un gesto de agradecimiento, puede considerar contribuir a su desarrollo realizando una <a href="%3$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>donación</strong></a>.</p>',

	'CLOUDFLARE_API_TOKEN' => 'Token de API',
	'CLOUDFLARE_API_TOKEN_EXPLAIN' => 'El token de la API de Cloudflare con los permisos <code>Zona:WAF de zona:Editar</code>, <code>Zona:Cache Rules:Editar</code> y <code>Zona:Purga de caché:Purgar</code>.',
	'CLOUDFLARE_ZONE_ID' => 'ID de Zona',
	'CLOUDFLARE_ZONE_ID_EXPLAIN' => 'El identificador de su dominio en Cloudflare.',

	'ACP_CLOUDFLARE_FIREWALL_SETTINGS' => 'Firewall settings',
	'ACP_CLOUDFLARE_CACHE_SETTINGS' => 'Cache settings',
	'ACP_CLOUDFLARE_RULESET_ID' => 'Ruleset',
	'ACP_CLOUDFLARE_RULESET_RULES_ID' => 'Rules',
	'ACP_CLOUDFLARE_SYNC' => 'Sync rules',
	'ACP_CLOUDFLARE_SYNC_EXPLAIN' => 'Click the <samp>%s</samp> button to create or update rules as needed.',
	'ACP_CLOUDFLARE_TOGGLE_SECRET' => 'Alternar visibilidad de %s',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Valores inválido para los campos: <samp>%s</samp>',

	'CLOUDFLARE_PURGE_CACHE' => 'Purgar la caché de Cloudflare',
	'CLOUDFLARE_PURGE_CACHE_EXPLAIN' => 'Purgar la caché de Cloudflare por tipo. Tome en cuenta que Cloudflare impone un límite de peticiones por segundo.',
	'CLOUDFLARE_PURGE_CACHE_TYPE_PURGE_EVERYTHING' => 'Purgar todo',
	'CLOUDFLARE_PURGE_CACHE_TYPE_HOSTS' => 'Nombres de host',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_TITLE' => 'Caché purgada',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_BODY' => 'La caché de Cloudflare se ha purgado correctamente.',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_TITLE' => 'Reglas del grupo de reglas actualizadas',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_BODY' => 'Las reglas del grupo de reglas de Cloudflare han sido actualizadas correctamente.'
]);
