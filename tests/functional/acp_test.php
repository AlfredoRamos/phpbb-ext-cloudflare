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
class acp_test extends \phpbb_functional_test_case
{
	use functional_test_case_trait;

	protected function setUp(): void
	{
		parent::setUp();
		$this->add_lang_ext('alfredoramos/cloudflare', 'captcha/turnstile');
		$this->login();
		$this->admin_login();
	}

	public function test_plugin_option()
	{
		$crawler = self::request('GET', sprintf('adm/index.php?i=acp_captcha&mode=visual&sid=%s', $this->sid));
		$form = $crawler->selectButton('configure')->form();

		$this->assertTrue($form->has('select_captcha'));
		$this->assertContains(
			'alfredoramos.cloudflare.captcha.turnstile',
			$form->get('select_captcha')->availableOptionValues()
		);

		$form->get('select_captcha')->select('alfredoramos.cloudflare.captcha.turnstile');
		$crawler = self::submit($form);
		$form = $crawler->selectButton('submit')->form();

		$this->assertTrue($form->has('turnstile_key'));
		$this->assertSame('', $form->get('turnstile_key')->getValue());

		$this->assertTrue($form->has('turnstile_secret'));
		$this->assertSame('', $form->get('turnstile_secret')->getValue());

		$this->assertTrue($form->has('turnstile_theme'));
		$this->assertSame('auto', $form->get('turnstile_theme')->getValue());
		$this->assertSame(
			['auto', 'light', 'dark'],
			$form->get('turnstile_theme')->availableOptionValues()
		);

		$this->assertTrue($form->has('turnstile_size'));
		$this->assertSame('normal', $form->get('turnstile_size')->getValue());
		$this->assertSame(
			['normal', 'flexible', 'compact'],
			$form->get('turnstile_size')->availableOptionValues()
		);

		$this->assertTrue($form->has('turnstile_appearance'));
		$this->assertSame('always', $form->get('turnstile_appearance')->getValue());
		$this->assertSame(
			['always', 'interaction-only'],
			$form->get('turnstile_appearance')->availableOptionValues()
		);

		$crawler = self::submit($form, [
			'turnstile_key' => '1x00000000000000000000AA',
			'turnstile_secret' => '1x0000000000000000000000000000000AA',
			'turnstile_theme' => 'dark',
			'turnstile_size' => 'compact',
			'turnstile_appearance' => 'interaction-only'
		]);

		$this->assertSame(1, $crawler->filter('.successbox')->count());

		$crawler = self::request('GET', sprintf('adm/index.php?i=acp_captcha&mode=visual&sid=%s', $this->sid));

		$form = $crawler->selectButton('main_submit')->form();
		$form->get('select_captcha')->select('alfredoramos.cloudflare.captcha.turnstile');
		self::submit($form);

		$crawler = self::request('GET', sprintf('adm/index.php?i=acp_captcha&mode=visual&sid=%s', $this->sid));

		$widget = $crawler->filter('.cf-turnstile');
		$this->assertSame(1, $widget->count());

		// ACP demo always uses test keys
		$this->assertSame(
			($widget->attr('data-appearance') === 'interaction-only') ? '1x00000000000000000000BB' : '3x00000000000000000000FF',
			$widget->attr('data-sitekey')
		);
		$this->assertSame(1, preg_match(
			'#^\dx[\w\-]{22}$#',
			$widget->attr('data-sitekey')
		));
		$this->assertSame('dark', $widget->attr('data-theme'));
		$this->assertSame('compact', $widget->attr('data-size'));
		$this->assertSame('interaction-only', $widget->attr('data-appearance'));

		$container = $crawler->filterXPath('//div[contains(@class, "cf-turnstile")]/ancestor::fieldset');
		$script = $crawler->filterXPath('//script[contains(@src, "cloudflare.com")]');
		$noscript = $container->filter('noscript');

		$this->assertSame(1, $script->count());
		$this->assertSame('https://challenges.cloudflare.com/turnstile/v0/api.js', $script->attr('src'));
		$this->assertSame(1, $noscript->count());
		$this->assertSame($this->lang('TURNSTILE_NOSCRIPT'), $noscript->filter('div')->text());
	}
}
