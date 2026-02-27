<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\controller;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\request\request;
use phpbb\controller\helper as controller_helper;
use phpbb\language\language;
use phpbb\user;
use phpbb\log\log;
use alfredoramos\cloudflare\includes\helper;

class acp
{
	/** @var config */
	protected $config;

	/** @var template */
	protected $template;

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

	/** @var helper */
	protected $helper;

	/**
	 * Controller constructor.
	 *
	 * @param config			$config
	 * @param template			$template
	 * @param request			$request
	 * @param controller_helper	$controller_helper
	 * @param language			$language
	 * @param user				$user
	 * @param log				$log
	 * @param helper			$helper
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, request $request, controller_helper $controller_helper, language $language, user $user, log $log, helper $helper)
	{
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->controller_helper = $controller_helper;
		$this->language = $language;
		$this->user = $user;
		$this->log = $log;
		$this->helper = $helper;
	}

	/**
	 * Settings mode page.
	 *
	 * @param string $u_action
	 *
	 * @return void
	 */
	public function settings_mode(string $u_action = ''): void
	{
		if (empty($u_action))
		{
			return;
		}

		// Validation errors
		$errors = [];

		// Field filters
		$filters = [
			'cloudflare_api_token' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#\A[A-Za-z0-9_-]{40}\z#'
				]
			],
			'cloudflare_zone_id' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#\A[A-Za-z0-9_]{32}\z#'
				]
			],
			'cloudflare_firewall_ruleset_id' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#\A(?:[A-Za-z0-9_]{32})?\z#'
				]
			],
			'cloudflare_firewall_ruleset_rules_id' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#\A(?:[A-Za-z0-9_]{32})?\z#'
				]
			],
			'cloudflare_cache_ruleset_id' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#\A(?:[A-Za-z0-9_]{32})?\z#'
				]
			],
			'cloudflare_cache_ruleset_rules_id' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#\A(?:[A-Za-z0-9_]{32})?\z#'
				]
			]
		];

		// Request form data
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('alfredoramos_cloudflare'))
			{
				trigger_error(
					$this->language->lang('FORM_INVALID') .
					adm_back_link($u_action),
					E_USER_WARNING
				);
			}

			// Form data
			$fields = [
				'cloudflare_api_token' => $this->request->variable('cloudflare_api_token', ''),
				'cloudflare_zone_id' => $this->request->variable('cloudflare_zone_id', ''),
				'cloudflare_firewall_ruleset_id' => $this->request->variable('cloudflare_firewall_ruleset_id', ''),
				'cloudflare_firewall_ruleset_rules_id' => $this->request->variable('cloudflare_firewall_ruleset_rules_id', ''),
				'cloudflare_cache_ruleset_id' => $this->request->variable('cloudflare_cache_ruleset_id', ''),
				'cloudflare_cache_ruleset_rules_id' => $this->request->variable('cloudflare_cache_ruleset_rules_id', '')
			];

			// Validation check
			if ($this->helper->validate($fields, $filters, $errors))
			{
				if (empty($fields['cloudflare_firewall_ruleset_id']))
				{
					$fields['cloudflare_firewall_ruleset_rules_id'] = '';
				}

				if (empty($fields['cloudflare_cache_ruleset_id']))
				{
					$fields['cloudflare_cache_ruleset_rules_id'] = '';
				}

				// Save configuration
				foreach ($fields as $key => $value)
				{
					$this->config->set($key, $value);
				}

				// Admin log
				$this->log->add(
					'admin',
					$this->user->data['user_id'],
					$this->user->ip,
					'LOG_CLOUDFLARE_DATA',
					false,
					[$this->language->lang('SETTINGS')]
				);

				// Confirm dialog
				trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($u_action));
			}
		}

		// Assign template variables
		$template_vars = [
			'ACP_CLOUDFLARE_SETTINGS_EXPLAIN' => $this->language->lang(
				'ACP_CLOUDFLARE_SETTINGS_EXPLAIN',
				$this->helper::SUPPORT_FAQ,
				$this->helper::SUPPORT_URL,
				$this->helper::VENDOR_DONATE,
			),
			'CLOUDFLARE_API_TOKEN' => $this->config->offsetGet('cloudflare_api_token'),
			'CLOUDFLARE_ZONE_ID' => $this->config->offsetGet('cloudflare_zone_id'),
			'CLOUDFLARE_FIREWALL_RULESET_ID' => $this->config->offsetGet('cloudflare_firewall_ruleset_id'),
			'CLOUDFLARE_FIREWALL_RULESET_RULES_ID' => $this->config->offsetGet('cloudflare_firewall_ruleset_rules_id'),
			'CLOUDFLARE_CACHE_RULESET_ID' => $this->config->offsetGet('cloudflare_cache_ruleset_id'),
			'CLOUDFLARE_CACHE_RULESET_RULES_ID' => $this->config->offsetGet('cloudflare_cache_ruleset_rules_id')
		];

		if (!empty($this->config->offsetGet('cloudflare_api_token')) && !empty($this->config->offsetGet('cloudflare_zone_id')))
		{
			$template_vars = array_merge([
				'CLOUDFLARE_FIREWALL_RULESET_RULES_SYNC_URL' => $this->controller_helper->route('alfredoramos_cloudflare_sync_ruleset_rules', [
					'type' => 'firewall',
					'hash' => generate_link_hash('cloudflare_firewall_sync_ruleset_rules')
				]),
				'CLOUDFLARE_CACHE_RULESET_RULES_SYNC_URL' => $this->controller_helper->route('alfredoramos_cloudflare_sync_ruleset_rules', [
					'type' => 'cache',
					'hash' => generate_link_hash('cloudflare_cache_sync_ruleset_rules')
				])
			], $template_vars);
		}

		$this->template->assign_vars($template_vars);

		// Validation errors
		foreach ($errors as $key => $value)
		{
			$this->template->assign_block_vars('VALIDATION_ERRORS', [
				'MESSAGE' => $value['message']
			]);
		}
	}
}
