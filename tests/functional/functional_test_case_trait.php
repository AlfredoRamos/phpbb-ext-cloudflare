<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\tests\functional;

trait functional_test_case_trait
{
	static protected function setup_extensions()
	{
		return ['alfredoramos/cloudflare'];
	}

	protected function init_turnstile()
	{
		$db = $this->get_db();
		$db->sql_transaction('begin');

		$config_ary = [
			// Turnstile
			'captcha_plugin' => 'alfredoramos.cloudflare.captcha.turnstile',
			'turnstile_key' => '1x00000000000000000000AA',
			'turnstile_secret' => '1x0000000000000000000000000000000AA',
			'turnstile_force_login' => '1'
		];

		foreach ($config_ary as $key => $value)
		{
			$key = trim($key);
			$value = trim($value);

			$sql = 'UPDATE ' . CONFIG_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', [
				'config_value' => $value
			]) . '
				WHERE ' . $db->sql_build_array('UPDATE', [
				'config_name' => $key
			]);

			$db->sql_query($sql);
		}

		$db->sql_transaction('commit');
	}
}
