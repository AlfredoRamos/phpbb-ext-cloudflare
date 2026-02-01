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
				'cloudflare_zone_id' => $this->request->variable('cloudflare_zone_id', '')
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
				$this->helper::SUPPORT_FAQ,
				$this->helper::SUPPORT_URL,
				$this->helper::VENDOR_DONATE,
			),
			'CLOUDFLARE_API_TOKEN' => $this->config->offsetGet('cloudflare_api_token'),
			'CLOUDFLARE_ZONE_ID' => $this->config->offsetGet('cloudflare_zone_id')
		]);

		// Validation errors
		foreach ($errors as $key => $value)
		{
			$this->template->assign_block_vars('VALIDATION_ERRORS', [
				'MESSAGE' => $value['message']
			]);
		}
	}
}
