<?php

namespace alfredoramos\cloudflare\tests\includes;

use alfredoramos\cloudflare\includes\http_trait;

class http_trait_test extends \phpbb_test_case
{
	public function test_client_reuses_matching_options_and_applies_new_options()
	{
		$fixture = new class {
			use http_trait;
			public function client(array $options)
			{
				return $this->get_client($options);
			}
		};
		$options = ['connect_timeout' => 5, 'timeout' => 10];
		$first = $fixture->client($options);
		$this->assertSame($first, $fixture->client($options));
		$this->assertSame(5, $first->getConfig('connect_timeout'));
		$this->assertSame(10, $first->getConfig('timeout'));
		$this->assertFalse($first->getConfig('allow_redirects'));
		$this->assertNotSame($first, $fixture->client(['timeout' => 20]));
		$this->assertFalse(property_exists($fixture, 'client_opts'));
	}
}
