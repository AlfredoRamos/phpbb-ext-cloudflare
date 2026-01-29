<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\request\request;

class listener implements EventSubscriberInterface
{
	/** @var request */
	protected $request;

	/**
	 * Listener constructor.
	 *
	 * @param request $request
	 *
	 * @return void
	 */
	public function __construct(request $request)
	{
		$this->request = $request;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.session_ip_after' => 'restore_original_ip'
		];
	}

	public function restore_original_ip($event): void
	{
		if (!empty($this->request->server('HTTP_CF_CONNECTING_IP')))
		{
			$event['ip'] = htmlspecialchars_decode($this->request->server('HTTP_CF_CONNECTING_IP'));
		}
	}
}
