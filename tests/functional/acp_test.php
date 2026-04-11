<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\tests\functional;

/**
 * @group functional
 */
class acp_test extends \phpbb_functional_test_case
{
	use functional_test_case_trait;

	protected function init(): void
	{
		$this->add_lang_ext('alfredoramos/cloudflare', ['captcha/turnstile', 'acp/settings']);
		$this->login();
		$this->admin_login();
	}

	public function test_captcha_plugin_settings()
	{
		$crawler = self::request('GET', 'adm/index.php?i=acp_captcha&mode=visual&sid=' . $this->sid);
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
		$this->assertSame('1x00000000000000000000AA', $form->get('turnstile_key')->getValue());

		$this->assertTrue($form->has('turnstile_secret'));
		$this->assertSame('1x0000000000000000000000000000000AA', $form->get('turnstile_secret')->getValue());

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
			'turnstile_key' => '1x00000000000000000000BB',
			'turnstile_secret' => '1x0000000000000000000000000000000AA',
			'turnstile_theme' => 'dark',
			'turnstile_size' => 'compact',
			'turnstile_appearance' => 'interaction-only'
		]);

		$this->assertSame(1, $crawler->filter('.successbox')->count());

		$crawler = self::request('GET', 'adm/index.php?i=acp_captcha&mode=visual&sid=' . $this->sid);

		$form = $crawler->selectButton('main_submit')->form();
		$form->get('select_captcha')->select('alfredoramos.cloudflare.captcha.turnstile');
		self::submit($form);

		$crawler = self::request('GET', 'adm/index.php?i=acp_captcha&mode=visual&sid=' . $this->sid);

		$widget = $crawler->filter('.cf-turnstile');
		$this->assertSame(1, $widget->count());

		// ACP demo always uses test keys
		$this->assertSame(
			($widget->attr('data-appearance') === 'interaction-only') ? '1x00000000000000000000BB' : '3x00000000000000000000FF',
			$widget->attr('data-sitekey')
		);
		$this->assertSame(1, preg_match('#\A\dx[A-Za-z0-9_-]{22}\z#',$widget->attr('data-sitekey')));
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

	public function test_settings_page()
	{
		$crawler = self::request('GET', 'adm/index.php?i=-alfredoramos-cloudflare-acp-main_module&mode=settings&sid=' . $this->sid);
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();

		$this->assertTrue($form->has('cloudflare_api_token'));
		$this->assertSame('cf_test_9f8d7c6b5a4e3d2c1b0a1234567890abcdef1234', $form->get('cloudflare_api_token')->getValue());

		$this->assertTrue($form->has('cloudflare_zone_id'));
		$this->assertSame('T3StL3x9Vw2Qm7Rz6Tn4Yb1C5D0eFhJkLpQsTuV', $form->get('cloudflare_zone_id')->getValue());

		$this->assertTrue($form->has('cloudflare_firewall_ruleset_id'));
		$this->assertTrue($form->has('cloudflare_firewall_ruleset_rules_id'));
		$this->assertTrue($form->has('cloudflare_cache_ruleset_id'));
		$this->assertTrue($form->has('cloudflare_cache_ruleset_rules_id'));

		$this->assertSame(2, $crawler->filter('.cf-rules-sync')->count());
		$this->assertTrue(str_contains($crawler->filter('.cf-rules-sync')->first()->attr('data-url'), '/cloudflare/sync_ruleset_rules/firewall/'));
		$this->assertTrue(str_contains($crawler->filter('.cf-rules-sync')->last()->attr('data-url'), '/cloudflare/sync_ruleset_rules/cache/'));
	}
}
