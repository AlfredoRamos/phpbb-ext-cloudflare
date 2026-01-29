<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\captcha\plugins;

use phpbb\captcha\plugins\captcha_abstract;
use phpbb\config\config;
use phpbb\user;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\language\language;
use phpbb\log\log_interface;
use alfredoramos\cloudflare\includes\helper;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class turnstile extends captcha_abstract
{
	/** @var string */
	private const SCRIPT_URL = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

	/** @var string */
	private const VERIFY_ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

	/** @var config */
	protected $config;

	/** @var user */
	protected $user;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var language */
	protected $language;

	/** @var log_interface */
	protected $log;

	/** @var helper */
	protected $helper;

	/** @var GuzzleClient */
	protected $client;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var array */
	protected $supported_values = [
		'theme' => ['auto', 'light', 'dark'],
		'size' => ['normal', 'flexible', 'compact'],
		'appearance' => ['always'] // Not yet implemented: execute, interaction-only
	];

	/**
	 * Constructor of Turnstile plugin.
	 *
	 * @param config		$config
	 * @param user			$user
	 * @param request		$request
	 * @param template		$template
	 * @param language		$language
	 * @param log_interface	$log
	 * @param helper		$helper
	 * @param string		$root_path
	 * @param string		$php_ext
	 *
	 * @return void
	 */
	public function __construct(config $config, user $user, request $request, template $template, language $language, log_interface $log, helper $helper, string $root_path, string $php_ext)
	{
		$this->config = $config;
		$this->user = $user;
		$this->request = $request;
		$this->template = $template;
		$this->language = $language;
		$this->log = $log;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * {@inheritDoc}
	 */
	public function init($type)
	{
		parent::init($type);
		$this->language->add_lang('captcha/turnstile', 'alfredoramos/cloudflare');
	}

	/**
	 * Not needed.
	 *
	 * @return void
	 */
	public function execute()
	{
	}

	/**
	 * Not needed.
	 *
	 * @return void
	 */
	public function execute_demo()
	{
	}

	/**
	 * Not needed.
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
	public function get_generator_class()
	{
		throw new \Exception('No generator class given.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_available()
	{
		$this->language->add_lang('captcha/turnstile', 'alfredoramos/cloudflare');
		return !empty($this->config->offsetGet('turnstile_key'))
			&& !empty($this->config->offsetGet('turnstile_secret'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function has_config()
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_name()
	{
		return 'CAPTCHA_TURNSTILE';
	}

	/**
	 * {@inheritDoc}
	 */
	public function acp_page($id, $module)
	{
		$module->tpl_name = '@alfredoramos_cloudflare/acp_captcha_turnstile';
		$module->page_title = 'ACP_VC_SETTINGS';

		$form_key = 'acp_captcha';
		add_form_key($form_key);

		// Validation errors
		$errors = [];

		// Field filters
		$filters = [
			'turnstile_key' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^\dx[\w\-]{22}$#'
				]
			],
			'turnstile_secret' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^\dx[\w\-]{33}$#'
				]
			],
			'turnstile_theme' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^(?:' . implode('|', $this->supported_values['theme']) . ')?$#'
				]
			],
			'turnstile_size' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^(?:' . implode('|', $this->supported_values['size']) . ')?$#'
				]
			],
			'turnstile_appearance' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^(?:' . implode('|', $this->supported_values['appearance']) . ')?$#'
				]
			]
		];

		// Request form data
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($module->u_action), E_USER_WARNING);
			}

			// Form data
			$fields = [
				'turnstile_key' => $this->request->variable('turnstile_key', ''),
				'turnstile_secret' => $this->request->variable('turnstile_secret', ''),
				'turnstile_theme' => $this->request->variable('turnstile_theme', $this->supported_values['theme'][0]),
				'turnstile_size' => $this->request->variable('turnstile_size', $this->supported_values['size'][0]),
				'turnstile_appearance' => $this->request->variable('turnstile_appearance', $this->supported_values['appearance'][0])
			];

			// Validation check
			if ($this->helper->validate($fields, $filters, $errors))
			{
				// Save configuration
				foreach ($fields as $key => $value)
				{
					$this->config->set($key, $value);
				}

				// Confirm dialog
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CONFIG_VISUAL');
				trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($module->u_action));
			}
		}

		// Assign template variables
		$this->template->assign_vars([
			'U_ACTION'			=> $module->u_action,

			'TURNSTILE_KEY'		=> $this->config->offsetGet('turnstile_key'),
			'TURNSTILE_SECRET'	=> $this->config->offsetGet('turnstile_secret'),

			'CAPTCHA_NAME'		=> $this->get_service_name(),
			'CAPTCHA_PREVIEW'	=> $this->get_demo_template($id),

			'S_TURNSTILE_SETTINGS'	=> true
		]);

		// Assign allowed values
		foreach ($this->supported_values as $key => $value)
		{
			$block_var = sprintf('TURNSTILE_%s_LIST', strtoupper($key));

			foreach ($value as $val)
			{
				$this->template->assign_block_vars($block_var, [
					'KEY' => $val,
					'NAME' => $this->language->lang(sprintf(
						'TURNSTILE_%1$s_%2$s',
						strtoupper($key),
						strtoupper($val)
					)),
					'ENABLED' => ($this->config->offsetGet(sprintf('turnstile_%s', $key)) === $val)
				]);
			}
		}

		// Assign validation errors
		foreach ($errors as $error)
		{
			$this->template->assign_block_vars('VALIDATION_ERRORS', [
				'MESSAGE' => $error['message']
			]);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_template()
	{
		if ($this->is_solved())
		{
			return false;
		}

		$contact = phpbb_get_board_contact_link($this->config, $this->root_path, $this->php_ext);
		$explain = $this->type !== CONFIRM_POST ? 'CONFIRM_EXPLAIN' : 'POST_CONFIRM_EXPLAIN';

		$this->template->assign_vars([
			'CONFIRM_EXPLAIN'		=> $this->language->lang($explain, '<a href="' . $contact . '">', '</a>'),
			'TURNSTILE_KEY'			=> $this->config->offsetGet('turnstile_key'),
			'TURNSTILE_THEME'		=> $this->config->offsetGet('turnstile_theme'),
			'TURNSTILE_SIZE'			=> $this->config->offsetGet('turnstile_size'),
			'U_TURNSTILE_SCRIPT'		=> self::SCRIPT_URL,
			'S_TURNSTILE_AVAILABLE'	=> $this->is_available(),
			'S_CONFIRM_CODE'		=> true,
			'S_TYPE'				=> $this->type
		]);

		return '@alfredoramos_cloudflare/captcha_turnstile.html';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_demo_template($id)
	{
		$this->template->assign_vars([
			'TURNSTILE_KEY'			=> $this->config->offsetGet('turnstile_key'),
			'TURNSTILE_THEME'		=> $this->config->offsetGet('turnstile_theme'),
			'TURNSTILE_SIZE'		=> $this->config->offsetGet('turnstile_size'),
			'TURNSTILE_APPEARANCE'	=> $this->config->offsetGet('turnstile_appearance'),
			'U_TURNSTILE_SCRIPT'	=> self::SCRIPT_URL,
			'S_TURNSTILE_AVAILABLE'	=> $this->is_available(),
		]);

		return '@alfredoramos_cloudflare/captcha_turnstile_demo.html';
	}

	/**
	 * Get Guzzle client
	 *
	 * @return GuzzleClient
	 */
	protected function get_client()
	{
		if (!isset($this->client))
		{
			$this->client = new GuzzleClient(['allow_redirects' => false]);
		}

		return $this->client;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate()
	{
		if (!parent::validate())
		{
			return false;
		}

		$result = $this->request->variable('cf-turnstile-response', '', true);

		if (empty($result))
		{
			return $this->language->lang('TURNSTILE_INCORRECT');
		}

		// Verify Turnstile token
		try
		{
			$client = $this->get_client();

			$response = $client->request('POST', self::VERIFY_ENDPOINT, [
				'form_params' => [
					'secret'	=> $this->config->offsetGet('turnstile_secret'),
					'response'	=> $result,
					'remoteip'	=> $this->user->ip,
					// TODO: idempotency_key
				]
			]);

			$data = json_decode($response->getBody()->getContents());

			if ($data->success === true)
			{
				$this->solved = true;
				return false;
			}
		}
		catch (GuzzleException $ex)
		{
			return $this->language->lang('TURNSTILE_REQUEST_EXCEPTION', $ex->getMessage());
		};

		return $this->language->lang('TURNSTILE_INCORRECT');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_login_error_attempts(): string
	{
		$this->language->add_lang('captcha/turnstile', 'alfredoramos/cloudflare');
		return 'TURNSTILE_LOGIN_ERROR_ATTEMPTS';
	}
}
