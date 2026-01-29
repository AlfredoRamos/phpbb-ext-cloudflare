<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\tests\functional;

/**
 * @group functional
 */
class ucp_test extends \phpbb_functional_test_case
{
	use functional_test_case_trait;

	protected function setUp(): void
	{
		parent::setUp();
		$this->add_lang_ext('alfredoramos/cloudflare', 'captcha/turnstile');
		$this->init_turnstile();
	}

	public function test_register_captcha()
	{
		$crawler = self::request('GET', 'ucp.php?mode=register');
		$form = $crawler->selectButton('agreed')->form();

		$crawler = self::submit($form);
		$container = $crawler->filter('.turnstile-container');
		$this->assertSame(1, $container->count());

		$widget = $container->filter('.cf-turnstile');
		$this->assertSame(1, $widget->count());
		$this->assertSame(
			'1x00000000000000000000AA',
			$widget->attr('data-sitekey')
		);
		$this->assertSame(
			1,
			preg_match(
				'#^\dx[\w\-]{22}$#',
				$widget->attr('data-sitekey')
			)
		);

		$script = $crawler->filterXPath('//script[contains(@src, "cloudflare.com")]');
		$this->assertSame(1, $script->count());
		$this->assertSame('https://challenges.cloudflare.com/turnstile/v0/api.js', $script->attr('src'));

		$noscript = $container->filter('noscript');
		$this->assertSame(1, $noscript->count());
		$this->assertSame(
			$this->lang('TURNSTILE_NOSCRIPT'),
			$noscript->filter('div')->text()
		);
	}
}
