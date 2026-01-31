<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\migrations\v10x;

use phpbb\db\migration\migration;

class m002_modules extends migration
{
	/**
	 * Add Cloudflare ACP settings.
	 *
	 * @return array
	 */
	public function update_data()
	{
		return [
			[
				'module.add',
				[
					'acp',
					'ACP_CAT_DOT_MODS',
					'ACP_CLOUDFLARE'
				]
			],
			[
				'module.add',
				[
					'acp',
					'ACP_CLOUDFLARE',
					[
						'module_basename' => '\alfredoramos\cloudflare\acp\main_module',
						'modes'	=> ['settings']
					]
				]
			]
		];
	}
}
