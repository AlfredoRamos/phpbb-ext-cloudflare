<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\tests\event;

use phpbb\request\request;
use alfredoramos\cloudflare\event\listener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group event
 */
class listener_test extends \phpbb_test_case
{
	protected $request;

	protected function setUp(): void
	{
		parent::setUp();
		$this->request = $this->getMockBuilder(request::class)
			->disableOriginalConstructor()->getMock();
	}

	public function test_instance()
	{
		$this->assertInstanceOf(
			EventSubscriberInterface::class,
			new listener($this->request)
		);
	}

	public function test_subscribed_events()
	{
		$this->assertSame(
			['core.session_ip_after'],
			array_keys(listener::getSubscribedEvents())
		);
	}
}
