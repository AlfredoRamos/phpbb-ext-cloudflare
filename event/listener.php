<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use alfredoramos\cloudflare\includes\helper;

class listener implements EventSubscriberInterface
{
	/** @var helper */
	protected $helper;

	/**
	 * Listener constructor.
	 *
	 * @param helper $helper
	 *
	 * @return void
	 */
	public function __construct(helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.session_ip_after' => 'restore_original_ip',
			'core.adm_page_header_after' => 'acp_global_template_variables',
			'core.login_box_before' => 'login_captcha'
		];
	}

	public function restore_original_ip($event): void
	{
		$ip = $this->helper->original_visitor_ip();

		if (!empty($ip))
		{
			$event['ip'] = $ip;
		}
	}

	public function acp_global_template_variables($event): void
	{
		$this->helper->acp_assign_template_variables();
	}

	public function login_captcha($event): void
	{
		if ($event['admin'])
		{
			return;
		}

		$this->helper->setup_login_captcha();
	}
}
