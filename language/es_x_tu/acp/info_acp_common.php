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
	'ACP_CLOUDFLARE' => 'Cloudflare',
	'LOG_CLOUDFLARE_DATA' => '<strong>Cambio en configuración de Cloudflare</strong><br>» %s',
	'LOG_CLOUDFLARE_PURGE_CACHE' => '<strong>Purga de caché de Cloudflare</strong><br>» %s'
]);
