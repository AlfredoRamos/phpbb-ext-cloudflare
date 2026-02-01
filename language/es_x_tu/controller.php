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
	'EXCEPTION_CLOUDFLARE_NO_API_DATA' => 'Cloudflare API key and Zone ID are mandatory.',
	'EXCEPTION_CLOUDFLARE_AJAX_ONLY' => 'This route can only be used on AJAX calls.',

	'CLOUDFLARE_ERR_PURGE_CACHE_TYPE' => 'Invalid purge cache type.'
]);
