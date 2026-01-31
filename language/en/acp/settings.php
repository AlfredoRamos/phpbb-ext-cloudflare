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
	'ACP_CLOUDFLARE_SETTINGS_EXPLAIN' => '<p>Here you can configure the API data for Cloudflare. Consult the <a href="%1$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>FAQ</strong></a> for more information. If you require assistance, please visit the <a href="%2$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>Support</strong></a> section.</p><p>If you like or found this extension useful and want to show some appreciation, you can consider supporting its development by <a href="%3$s" rel="external nofollow noreferrer noopener" target="_blank"><strong>giving a donation</strong></a>.</p>',

	'ACP_CLOUDFLARE_API_TOKEN' => 'API Token',
	'ACP_CLOUDFLARE_API_TOKEN_EXPLAIN' => 'Cloudflare API token with enough permissions to purge the zone cache (<code>Zone.Cache Purge</code>).',
	'ACP_CLOUDFLARE_ZONE_ID' => 'Zone ID',
	'ACP_CLOUDFLARE_ZONE_ID_EXPLAIN' => 'The identifier of your domain on Cloudflare.',

	'CLOUDFLARE_PURGE_CACHE' => 'Purge the Cloudflare cache',
	'CLOUDFLARE_PURGE_CACHE_EXPLAIN' => 'Purge Cloudflare cache by type. Note that Cloudflare impose a limit of request per seconds.',
	'CLOUDFLARE_PURGE_CACHE_TYPE_PURGE_EVERYTHING' => 'Purge everything',
	'CLOUDFLARE_PURGE_CACHE_TYPE_HOSTS' => 'Hosts',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_TITLE' => 'Cache purged',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_BODY' => 'The Cloudflare cache has been successfully purged.',
]);
