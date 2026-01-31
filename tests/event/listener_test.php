<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\tests\event;

use alfredoramos\cloudflare\event\listener;
use alfredoramos\cloudflare\includes\helper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group event
 */
class listener_test extends \phpbb_test_case
{
	protected $helper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->helper = $this->getMockBuilder(helper::class)
			->disableOriginalConstructor()->getMock();
	}

	public function test_instance()
	{
		$this->assertInstanceOf(
			EventSubscriberInterface::class,
			new listener($this->helper)
		);
	}

	public function test_subscribed_events()
	{
		$this->assertSame(
			[
				'core.session_ip_after',
				'core.download_file_send_to_browser_before',
				'core.adm_page_header_after'
			],
			array_keys(listener::getSubscribedEvents())
		);
	}
}
