<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\controller;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\request\request;
use phpbb\controller\helper as controller_helper;
use phpbb\language\language;
use phpbb\user;
use phpbb\log\log;
use phpbb\exception\runtime_exception;
use phpbb\exception\http_exception;
use phpbb\json_response;
use alfredoramos\cloudflare\includes\cloudflare as cloudflare_client;
use alfredoramos\cloudflare\includes\helper;
use Symfony\Component\HttpFoundation\JsonResponse;

class cloudflare
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var request */
	protected $request;

	/** @var controller_helper */
	protected $controller_helper;

	/** @var language */
	protected $language;

	/** @var user */
	protected $user;

	/** @var log */
	protected $log;

	/** @var cloudflare_client */
	protected $client;

	/** @var helper */
	protected $helper;

	/**
	 * Controller constructor.
	 *
	 * @param auth				$auth
	 * @param config			$config
	 * @param request			$request
	 * @param controller_helper	$controller_helper
	 * @param language			$language
	 * @param user				$user
	 * @param log				$log
	 * @param cloudflare_client	$client
	 * @param helper			$helper
	 *
	 * @return void
	 */
	public function __construct(auth $auth, config $config, request $request, controller_helper $controller_helper, language $language, user $user, log $log, cloudflare_client $client, helper $helper)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->request = $request;
		$this->controller_helper = $controller_helper;
		$this->language = $language;
		$this->user = $user;
		$this->log = $log;
		$this->client = $client;
		$this->helper = $helper;

		$this->client->set_options([
			'api_token' => $this->config->offsetGet('cloudflare_api_token'),
			'zone_id' => $this->config->offsetGet('cloudflare_zone_id')
		]);
	}

	/**
	 * Cloudflare purge cache controller handler.
	 *
	 * @param string $hash
	 *
	 * @throws http_exception
	 *
	 * @return JsonResponse
	 */
	public function purge_cache(string $hash = ''): JsonResponse
	{
		// This route can only be used by founder admins
		// Other users do not need to know this page exist
		if (!$this->auth->acl_get('a_') || (int) $this->user->data['user_type'] !== USER_FOUNDER) {
			throw new http_exception(404, 'PAGE_NOT_FOUND');
		}

		// Load translations
		$this->language->add_lang(['controller', 'acp/info_acp_common', 'acp/settings'], 'alfredoramos/cloudflare');

		// This route only responds to AJAX calls
		if (!$this->request->is_ajax()) {
			throw new runtime_exception('EXCEPTION_CLOUDFLARE_AJAX_ONLY');
		}

		// Security hash
		$hash = trim($hash);

		// CSRF protection
		if (empty($hash) || !check_link_hash($hash, 'cloudflare_purge_cache')) {
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Mandatory API data
		if (empty($this->config->offsetGet('cloudflare_api_token')) || empty($this->config->offsetGet('cloudflare_zone_id'))) {
			throw new runtime_exception('EXCEPTION_CLOUDFLARE_NO_API_DATA');
		}

		$errors = [];

		$fields = [
			'type' => $this->request->variable('cloudflare_purge_cache_type', $this->client::PURGE_CACHE_TYPES[0]),
			'value' => $this->request->variable('cloudflare_purge_cache_value', '')
		];

		if (empty($fields['type']) || !in_array($fields['type'], $this->client::PURGE_CACHE_TYPES))
		{
			$errors[]['message'] = $this->language->lang('CLOUDFLARE_ERR_PURGE_CACHE_TYPE');
		}

		if (!empty($errors)) {
			return new JsonResponse($errors, 400);
		}

		$data = [];
		$payload = ['type' => $fields['type'], 'value' => null];

		if (!$fields['type'] !== 'purge_everything')
		{
			$fields['value'] = $this->helper->sanitize_string_list(explode(PHP_EOL, $fields['value']));
		}

		switch($fields['type'])
		{
			case 'purge_everything':
				$payload['value'] = true;
				break;

			case 'hosts':
				$payload['value'] = (empty($fields['value'])) ? [$this->config->offsetGet('server_name')] : $fields['value'];
				break;
		}

		if (empty($fields['value']))
		{
			$errors[]['message'] = $this->language->lang('AJAX_ERROR_TEXT_PARSERERROR');
		}
		else
		{
			$data = $this->client->purge_cache($payload);

			if ((empty($data['success']) || $data['success'] !== true) && !empty($data['errors']))
			{
				$errors = array_merge($errors, $data['errors']);
			}
		}

		if (!empty($errors)) {
			return new JsonResponse($errors, 400);
		}

		// Admin log
		$this->log->add(
			'admin',
			$this->user->data['user_id'],
			$this->user->ip,
			'LOG_CLOUDFLARE_PURGE_CACHE',
			false,
			[$this->language->lang(sprintf('CLOUDFLARE_PURGE_CACHE_TYPE_%s', strtoupper($fields['type'])))]
		);

		return new JsonResponse($data);
	}

	public function sync_ruleset_rules(string $type = '', string $hash = ''): JsonResponse
	{
		// This route can only be used by founder admins
		// Other users do not need to know this page exist
		if (!$this->auth->acl_get('a_') || (int) $this->user->data['user_type'] !== USER_FOUNDER) {
			throw new http_exception(404, 'PAGE_NOT_FOUND');
		}

		// Load translations
		$this->language->add_lang(['controller', 'acp/info_acp_common', 'acp/settings'], 'alfredoramos/cloudflare');

		// This route only responds to AJAX calls
		if (!$this->request->is_ajax()) {
			throw new runtime_exception('EXCEPTION_CLOUDFLARE_AJAX_ONLY');
		}

		// Security hash
		$hash = trim($hash);

		// CSRF protection
		if (empty($hash) || !check_link_hash($hash, 'cloudflare_sync_ruleset_rules')) {
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Mandatory API data
		if (empty($this->config->offsetGet('cloudflare_api_token')) || empty($this->config->offsetGet('cloudflare_zone_id'))) {
			throw new runtime_exception('EXCEPTION_CLOUDFLARE_NO_API_DATA');
		}

		$errors = [];
		$allowed = ['firewall', 'cache'];
		$type = trim($type);

		if (!in_array($type, $allowed, true))
		{
			$errors[]['message'] = $this->language->lang('CLOUDFLARE_ERR_RULESET_TYPE');
		}

		if (!empty($errors)) {
			return new JsonResponse($errors, 400);
		}

		$fields = [
			'ruleset_id' => $this->config->offsetGet(sprintf('cloudflare_%s_ruleset_id', $type)) ?? '',
			'ruleset_rules_id' => $this->config->offsetGet(sprintf('cloudflare_%s_ruleset_rules_id', $type)) ?? ''
		];

		$payload = [];

		if (empty($fields['ruleset_id']))
		{
			$ruleset = null;
			$data = [];

			switch($type)
			{
				case 'firewall':
					$ruleset = $this->client->find_ruleset(['phase' => 'http_request_firewall_custom']);

					$data = [
						'name' => 'Firewall ruleset',
						'description' => 'Created by Cloudflare extension for phpBB https://alfredoramos.mx/cloudflare-extension-for-phpbb/',
						'kind' => 'zone',
						'phase' => 'http_request_firewall_custom'
					];
					break;

				case 'cache':
					$ruleset = $this->client->find_ruleset(['phase' => 'http_request_cache_settings']);

					$data = [
						'name' => 'Cache ruleset',
						'description' => 'Created by Cloudflare extension for phpBB https://alfredoramos.mx/cloudflare-extension-for-phpbb/',
						'kind' => 'zone',
						'phase' => 'http_request_cache_settings'
					];
					break;
			}

			if (empty($ruleset['id']))
			{
				$ruleset = $this->client->create_ruleset($data);

				if (empty($ruleset['result']['id']))
				{
					$errors[]['message'] = $this->language->lang('CLOUDFLARE_ERR_RULESET_TYPE');
				}
				else
				{
					$fields['ruleset_id'] = $ruleset['result']['id'];
					$this->config->set(sprintf('cloudflare_%s_ruleset_id', $type), $ruleset['result']['id']);
				}
			}
			else
			{
				$fields['ruleset_id'] = $ruleset['id'];
				$this->config->set(sprintf('cloudflare_%s_ruleset_id', $type), $ruleset['id']);
			}
		}
		/*else
		{
			$data = [];

			switch($type)
			{
				case 'firewall':
					$data = [
						//'name' => 'Firewall ruleset',
						'description' => 'Created by Cloudflare extension for phpBB https://alfredoramos.mx/cloudflare-extension-for-phpbb/',
						'kind' => 'zone',
						'phase' => 'http_request_firewall_custom'
					];
					break;

				case 'cache':
					$data = [
						//'name' => 'Cache ruleset',
						'description' => 'Created by Cloudflare extension for phpBB https://alfredoramos.mx/cloudflare-extension-for-phpbb/',
						'kind' => 'zone',
						'phase' => 'http_request_cache_settings'
					];
					break;
			}

			if (empty($data))
			{
				$errors[]['message'] = $this->language->lang('CLOUDFLARE_ERR_RULESET_UPDATE');
			}
			else
			{
				$this->client->update_ruleset($fields['ruleset_id'], $data);
			}
		}*/

		if (!empty($errors)) {
			return new JsonResponse($errors, 400);
		}

		if (empty($fields['ruleset_rules_id']))
		{
			$rules = [];
			$data = [];

			switch($type)
			{
				case 'firewall':
					$rules = $this->client->find_ruleset_rules($fields['ruleset_id'], [
						'action' => 'managed_challenge',
						'description' => 'phpbb:firewall'
					], true);

					$data = [
						'description' => 'phpbb:firewall',
						'expression' => '(http.request.uri.path contains "ucp.php" and (http.request.uri.query contains "mode=login" or http.request.uri.query contains "mode=register")) or (http.request.uri.path contains "memberlist.php" and http.request.uri.query contains "mode=contactadmin")',
						'action' => 'managed_challenge',
						'position' => [
							'index' => 1,
						]
					];
					break;

				case 'cache':
					$rules = $this->client->find_ruleset_rules($fields['ruleset_id'], [
						'action' => 'set_cache_settings',
						'description' => 'phpbb:cache'
					], true);

					$data = [
						'description' => 'phpbb:cache',
						'expression' => '(http.request.uri.path contains "file.php" and http.request.uri.query wildcard r"*avatar=*")',
						'action' => 'set_cache_settings',
						'action_parameters' => [
							'cache' => true,
							'cache_key' => ['cache_deception_armor' => true]
						]
					];
					break;
			}

			if (empty($rules['id']))
			{
				// TODO: Fix duplication
				$rules = $this->client->create_ruleset_rules($fields['ruleset_id'], $data);

				if (empty($rules['result']['rules']))
				{
					$errors[]['message'] = $this->language->lang('CLOUDFLARE_ERR_RULESET_RULES_LIST');
				}
				else
				{
					$fields['ruleset_rules_id'] = $rules['result'][0]['id'];
					$this->config->set(sprintf('cloudflare_%s_ruleset_rules_id', $type), $rules['result'][0]['id']);
				}
			}
			else
			{
				$fields['ruleset_rules_id'] = $rules['id'];
				$this->config->set(sprintf('cloudflare_%s_ruleset_rules_id', $type), $rules['id']);
			}
		}
		else
		{
			$data = [];

			switch($type)
			{
				case 'firewall':
					$data = [
						'description' => 'phpbb:firewall',
						'expression' => '(http.request.uri.path contains "ucp.php" and (http.request.uri.query contains "mode=login" or http.request.uri.query "mode=register")) or (http.request.uri.path contains "memberlist.php" and http.request.uri.query "mode=contactadmin")',
						'action' => 'managed_challenge',
					];
					break;

				case 'cache':
					$data = [
						'description' => 'phpbb:cache',
						'expression' => '(http.request.uri.path contains "file.php" and http.request.uri.query wildcard r"*avatar=*")',
						'action' => 'set_cache_settings',
						'action_parameters' => [
							'cache' => true,
							'cache_key' => ['cache_deception_armor' => true]
						]
					];
					break;
			}

			if (empty($data))
			{
				$errors[]['message'] = $this->language->lang('CLOUDFLARE_ERR_RULESET_RULES_UPDATE');
			}
			else
			{
				$this->client->update_ruleset_rules($fields['ruleset_id'], $fields['ruleset_rules_id'], $data);
			}
		}

		if (!empty($errors)) {
			return new JsonResponse($errors, 400);
		}

		return new JsonResponse([
			'success' => true,
			'ruleset_id' => $fields['ruleset_id'],
			'ruleset_rules_id' => $fields['ruleset_rules_id']
		]);
	}
}
