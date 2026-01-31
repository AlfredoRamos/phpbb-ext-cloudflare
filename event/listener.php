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
			'core.download_file_send_to_browser_before' => 'cache_attachments',
			'core.adm_page_header_after' => 'acp_global_template_variables'
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

	public function cache_attachments($event)
	{
		if (!$this->helper->is_public_attachment($event['attachment']))
		{
			return;
		}

		$tags = ['attachment'];
		$category = null;

		switch($event['display_cat'])
		{
			case ATTACHMENT_CATEGORY_IMAGE:
				$category = 'image';
				break;
			case ATTACHMENT_CATEGORY_THUMB:
				$category = 'thumbnail';
				break;
		}

		if (!empty($category))
		{
			$tags[] = $category;
		}

		$this->helper->cache_headers($event['attachment'], $tags);
	}

	public function acp_global_template_variables($event)
	{
		$this->helper->acp_assign_template_variables();
	}
}
