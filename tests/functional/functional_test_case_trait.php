<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\tests\functional;

trait functional_test_case_trait
{
	static protected function setup_extensions()
	{
		return ['alfredoramos/cloudflare'];
	}

	abstract protected function init(): void;

	protected function setUp(): void
	{
		parent::setUp();

		$this->update_config([
			// Cloudflare API
			'cloudflare_api_token' => 'cf_test_9f8d7c6b5a4e3d2c1b0a1234567890abcdef1234', // Fake
			'cloudflare_zone_id' => '5d14bf0dfa6eb316cb18292586562ba3', // Fake

			// Cloudflare Turnstile
			'captcha_plugin' => 'alfredoramos.cloudflare.captcha.turnstile',
			'turnstile_key' => '1x00000000000000000000AA',
			'turnstile_secret' => '1x0000000000000000000000000000000AA',
			'turnstile_force_login' => '1'
		]);

		$this->init();
	}

	private function update_config(array $data = []): void
	{
		if (empty($data))
		{
			return;
		}

		$db = $this->get_db();
		$db->sql_transaction('begin');

		foreach ($data as $key => $value)
		{
			if (!is_string($key) || !is_string($value))
			{
				continue;
			}

			$key = trim($key);
			$value = trim($value);

			if (empty($key))
			{
				continue;
			}

			$sql = 'UPDATE ' . CONFIG_TABLE . '
			SET ' . $db->sql_build_array('UPDATE',
				[
					'config_value' => $value,
					'is_dynamic' => 1 // Fix cache
				]
			) . '
			WHERE ' . $db->sql_build_array('UPDATE',
				['config_name' => $key]
			);
			$db->sql_query($sql);
		}

		$db->sql_transaction('commit');
	}
}
