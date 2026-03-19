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

	'CLOUDFLARE_API_TOKEN' => 'API Token',
	'CLOUDFLARE_API_TOKEN_EXPLAIN' => 'Cloudflare API token with the permissions <code>Zone:Zone WAF:Edit</code>, <code>Zone:Cache Rules:Edit</code> and <code>Zone:Cache Purge:Purge</code>.',
	'CLOUDFLARE_ZONE_ID' => 'Zone ID',
	'CLOUDFLARE_ZONE_ID_EXPLAIN' => 'The identifier of your domain on Cloudflare.',

	'ACP_CLOUDFLARE_FIREWALL_SETTINGS' => 'Firewall settings',
	'ACP_CLOUDFLARE_CACHE_SETTINGS' => 'Cache settings',
	'ACP_CLOUDFLARE_RULESET_ID' => 'Ruleset',
	'ACP_CLOUDFLARE_RULESET_RULES_ID' => 'Rules',
	'ACP_CLOUDFLARE_SYNC' => 'Sync rules',
	'ACP_CLOUDFLARE_SYNC_EXPLAIN' => 'Click the <samp>%s</samp> button to create or update rules as needed.',
	'ACP_CLOUDFLARE_TOGGLE_SECRET' => 'Activer/désactiver %s',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Valeurs invalides pour les champs : <samp>%s</samp>',

	'CLOUDFLARE_PURGE_CACHE' => 'Purger le cache Cloudflare',
	'CLOUDFLARE_PURGE_CACHE_EXPLAIN' => 'Purge Cloudflare cache by type. Note that Cloudflare impose a limit of request per seconds.',
	'CLOUDFLARE_PURGE_CACHE_TYPE_PURGE_EVERYTHING' => 'Tout purger',
	'CLOUDFLARE_PURGE_CACHE_TYPE_HOSTS' => 'Hôtes',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_TITLE' => 'Cache purged',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_BODY' => 'Les règles de Cloudflare ont été mises à jour avec succès.',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_TITLE' => 'Règles mises à jour',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_BODY' => 'Les règles de Cloudflare ont été mises à jour avec succès.'
]);
