<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\migrations\v10x;

use phpbb\db\migration\migration;

class m001_config extends migration
{
	/**
	 * Add Turnstile configuration.
	 *
	 * @return array
	 */
	public function update_data()
	{
		return [
			// Cloudflare
			[
				'config.add',
				['cloudflare_api_token', '']
			],
			[
				'config.add',
				['cloudflare_zone_id', '']
			],

			// Turnstile
			[
				'config.add',
				['turnstile_key', '']
			],
			[
				'config.add',
				['turnstile_secret', '']
			],
			[
				'config.add',
				['turnstile_theme', 'auto']
			],
			[
				'config.add',
				['turnstile_size', 'normal']
			],
			[
				'config.add',
				['turnstile_appearance', 'always']
			],
			[
				'config.add',
				['turnstile_force_login', 1]
			]
		];
	}
}
