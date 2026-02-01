<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\includes;

use phpbb\config\config;
use phpbb\request\request;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\routing\helper as routing_helper;
use phpbb\captcha\factory as captcha_factory;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use alfredoramos\cloudflare\includes\cloudflare as cloudflare_client;

class helper
{
	/** @var config */
	protected $config;

	/** @var request */
	protected $request;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/** @var routing_helper */
	protected $routing_helper;

	/** @var captcha_factory */
	protected $captcha_factory;

	/** @var cloudflare_client */
	protected $cloudflare_client;

	/** @var string */
	public const SUPPORT_FAQ = 'https://www.phpbb.com/customise/db/extension/cloudflare/faq';

	/** @var string */
	public const SUPPORT_URL = 'https://www.phpbb.com/customise/db/extension/cloudflare/support';

	/** @var string */
	public const VENDOR_DONATE = 'https://alfredoramos.mx/donate/';

	/**
	 * Helper constructor.
	 *
	 * @param config				$config
	 * @param request				$request
	 * @param language				$language
	 * @param template				$template
	 * @param routing_helper		$routing_helper
	 * @param captcha_factory		$captcha_factory
	 * @param cloudflare_client		$cloudflare_client
	 *
	 * @param void
	 */
	public function __construct(config $config, request $request, language $language, template $template, routing_helper $routing_helper, captcha_factory $captcha_factory, cloudflare_client $cloudflare_client)
	{
		$this->config = $config;
		$this->request = $request;
		$this->language = $language;
		$this->template = $template;
		$this->routing_helper = $routing_helper;
		$this->captcha_factory = $captcha_factory;
		$this->cloudflare_client = $cloudflare_client;
	}

	/**
	 * Validate form fields with given filters.
	 *
	 * @param array $fields		Pair of field name and value
	 * @param array $filters	Filters that will be passed to filter_var_array()
	 * @param array $errors		Array of message errors
	 *
	 * @return bool
	 */
	public function validate(array &$fields = [], array &$filters = [], array &$errors = []): bool
	{
		if (empty($fields) || empty($filters))
		{
			return false;
		}

		// Filter fields
		$data = filter_var_array($fields, $filters, false);

		// Invalid fields helper
		$invalid = [];

		// Validate fields
		foreach ($data as $key => $value)
		{
			// Remove and generate error if field did not pass validation
			// Not using empty() because an empty string can be a valid value
			if (!isset($value) || $value === false)
			{
				$invalid[] = $this->language->lang(sprintf('%s', strtoupper($key)));
				unset($fields[$key]);
			}
		}

		if (!empty($invalid))
		{
			$errors[]['message'] = $this->language->lang(
				'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS',
				implode($this->language->lang('COMMA_SEPARATOR'), $invalid)
			);
		}

		// Validation check
		return empty($errors);
	}

	public function original_visitor_ip(): string|null
	{
		$ip = null;

		if (!empty($this->request->server('HTTP_CF_CONNECTING_IP')))
		{
			$ip = htmlspecialchars_decode($this->request->server('HTTP_CF_CONNECTING_IP'));
		}

		return $ip;
	}

	public function sanitize_string_list(array $list = []): array
	{
		$ary = [];

		foreach ($list as $item)
		{
			if (!is_string($item))
			{
				continue;
			}

			$item = filter_var(trim($item), FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_EMPTY_STRING_NULL|FILTER_FLAG_STRIP_BACKTICK);

			if (empty($item) || in_array($item, $ary))
			{
				continue;
			}

			$ary[] = $item;
		}

		return $ary;
	}

	public function uuid_v4(): string
	{
		$data = random_bytes(16);

		// Set version to 0100
		$data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

		// Set variant to 10
		$data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	// * delay = base_delay × 2^(attempt − 1) + jitter
	public function backoff_delay(int $attempt, int $base_ms = 250, int $max_ms = 2500): void {
		// Exponential backoff
		$delay_ms = min($base_ms * (2 ** ($attempt - 1)), $max_ms);

		// Add jitter (±25%)
		$jitter = random_int((int) ($delay_ms * -0.25), (int) ($delay_ms *  0.25));

		usleep(($delay_ms + $jitter) * 1000);
	}

	public function acp_assign_template_variables()
	{
		$this->language->add_lang(['acp/settings'], 'alfredoramos/cloudflare');

		$this->template->assign_vars([
			'BOARD_URL' => generate_board_url(),
			'CLOUDFLARE_PURGE_CACHE_URL' => $this->routing_helper->route('alfredoramos_cloudflare_purge_cache', [
				'hash' => generate_link_hash('cloudflare_purge_cache')
			])
		]);

		foreach ($this->cloudflare_client::PURGE_CACHE_TYPES as $key => $value)
		{
			$this->template->assign_block_vars('CLOUDFLARE_PURGE_CACHE_TYPES', [
				'NAME' => $this->language->lang(sprintf('CLOUDFLARE_PURGE_CACHE_TYPE_%s', strtoupper($value))),
				'VALUE' => $value,
			]);
		}
	}

	public function setup_login_captcha(): void
	{
		if ((int) $this->config->offsetGet('turnstile_force_login') !== 1)
		{
			return;
		}

		try
		{
			$captcha = $this->captcha_factory->get_instance('alfredoramos.cloudflare.captcha.turnstile');

			if (empty($captcha) || !$captcha->is_available())
			{
				return;
			}

			$captcha->init(CONFIRM_LOGIN);
			$this->template->assign_vars(['CAPTCHA_TEMPLATE' => $captcha->get_template()]);
		}
		catch(ServiceNotFoundException $ex) // Just in case, must not get here
		{
			return;
		}
	}
}
