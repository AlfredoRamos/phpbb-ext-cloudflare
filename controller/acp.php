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
	 * @param config	$config
	 * @param template	$template
	 * @param request	$request
	 * @param language	$language
	 * @param user		$user
	 * @param log		$log
	 * @param helper	$helper
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, request $request, language $language, user $user, log $log, helper $helper)
	{
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
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
					'regexp' => '#^\w{40}$#'
				]
			],
			'cloudflare_zone_id' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^\w{32}$#'
				]
			],
			'cloudflare_cache_time' => [
				'filter' => FILTER_VALIDATE_INT,
				'options' => [
					'min_range' => $this->helper::MIN_CACHE_TIME,
					'max_range' => $this->helper::MAX_CACHE_TIME
				]
			],
			'cloudflare_cache_type' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^(?:' . implode('|', $this->helper::SUPPORTED_CACHE_UNITS) . ')$#'
				]
			],
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
				'cloudflare_cache_time' => $this->request->variable('cloudflare_cache_time', 7),
				'cloudflare_cache_type' => $this->request->variable('cloudflare_cache_type', $this->helper::SUPPORTED_CACHE_UNITS[1]),
			];

			// Validation check
			if ($this->helper->validate($fields, $filters, $errors))
			{
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
		$this->template->assign_vars([
			'ACP_CLOUDFLARE_SETTINGS_EXPLAIN' => $this->language->lang(
				'ACP_CLOUDFLARE_SETTINGS_EXPLAIN',
				'https://www.phpbb.com/customise/db/extension/cloudflare/faq',
				'https://www.phpbb.com/customise/db/extension/cloudflare/support',
				'https://alfredoramos.mx/donate/'
			),
			'CLOUDFLARE_API_TOKEN' => $this->config->offsetGet('cloudflare_api_token'),
			'CLOUDFLARE_ZONE_ID' => $this->config->offsetGet('cloudflare_zone_id'),
			'CLOUDFLARE_CACHE_TIME' => (int) $this->config->offsetGet('cloudflare_cache_time'),
			'CLOUDFLARE_MIN_CACHE_TIME' => $this->helper::MIN_CACHE_TIME,
			'CLOUDFLARE_MAX_CACHE_TIME' => $this->helper::MAX_CACHE_TIME,
			'CLOUDFLARE_CACHE_TYPE' => $this->config->offsetGet('cloudflare_cache_type')
		]);

		// Cloudflare cache types
		foreach ($this->helper::SUPPORTED_CACHE_UNITS as $key => $value)
		{
			$unit_key = '';

			switch($value) {
				case 'h':
					$unit_key = 'HOURS';
					break;

				case 'd':
					$unit_key = 'DAYS';
					break;
			}

			$this->template->assign_block_vars('CLOUDFLARE_CACHE_UNITS', [
				'NAME' => $this->language->lang($unit_key),
				'VALUE' => $value,
				'SELECTED' => ($value === $this->config->offsetGet('cloudflare_cache_type'))
			]);
		}

		// Validation errors
		foreach ($errors as $key => $value)
		{
			$this->template->assign_block_vars('VALIDATION_ERRORS', [
				'MESSAGE' => $value['message']
			]);
		}
	}
}
