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
	'EXCEPTION_CLOUDFLARE_NO_API_DATA' => 'El token de API de Cloudflare y el ID de Zona son obligatorios.',
	'EXCEPTION_CLOUDFLARE_AJAX_ONLY' => 'Esta ruta sólo se puede utilizar en las llamadas AJAX.',

	'CLOUDFLARE_ERR_PURGE_CACHE_TYPE' => 'El tipo de caché a purgar es inválido.',
	'CLOUDFLARE_ERR_RULESET_TYPE' => 'El tipo del grupo de reglas es inválido.',
	'CLOUDFLARE_ERR_RULESET_UPDATE' => 'No se pudo actualizar grupo de reglas.',
	'CLOUDFLARE_ERR_RULESET_RULES_LIST' => 'La lista de reglas del grupo de reglas son inválidas.',
	'CLOUDFLARE_ERR_RULESET_RULES_UPDATE' => 'No se pudo actualizar las reglas del grupo de reglas.'
]);
