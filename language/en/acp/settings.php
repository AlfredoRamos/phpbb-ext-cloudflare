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
	'ACP_CLOUDFLARE_TOGGLE_SECRET' => 'Toggle %s',
	'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS' => 'Invalid values for fields: <samp>%s</samp>',
	'ACP_CLOUDFLARE_NOT_PROTECTED_EXPLAIN' => 'Your domain %s does not seem to be protected by Cloudflare.',
	'ACP_CLOUDFLARE_DOMAIN_MISMATCH_EXPLAIN' => 'The domain configured in Cloudflare (<samp>%1$s</samp>) does not match the one configured in your phpBB board (<samp>%2$s</samp>).',

	'ACP_CLOUDFLARE_CACHE_TIME' => 'Cache time',
	'ACP_CLOUDFLARE_CACHE_TIME_EXPLAIN' => 'Default time to store attachments in Cloudflare cache.',

	'CLOUDFLARE_PURGE_CACHE' => 'Purge the Cloudflare cache',
	'CLOUDFLARE_PURGE_CACHE_EXPLAIN' => 'Purge Cloudflare cache by type. Note that Cloudflare impose a limit of request per seconds.',
	'CLOUDFLARE_PURGE_CACHE_TYPE_PURGE_EVERYTHING' => 'Purge everything',
	'CLOUDFLARE_PURGE_CACHE_TYPE_HOSTS' => 'Hosts',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_TITLE' => 'Cache purged',
	'CLOUDFLARE_PURGE_CACHE_SUCCESS_BODY' => 'The Cloudflare cache has been successfully purged.',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_TITLE' => 'Ruleset rules updated',
	'CLOUDFLARE_RULESET_RULES_SUCCESS_BODY' => 'The Cloudflare ruleset rules have been successfully updated.'
]);
