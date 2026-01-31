<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\includes;

use GuzzleHttp\Client as GuzzleClient;

trait http_trait
{
	/** @var GuzzleClient */
	protected ?GuzzleClient $client = null;

	/** @var array */
	protected  array $client_options = [];

	/**
	 * Get Guzzle client
	 *
	 * @return GuzzleClient
	 */
	protected function get_client(?array $opts = null): GuzzleClient
	{
		$default = ['allow_redirects' => false];
		$opts = array_replace($default, $opts ?? []);

		if ($this->client === null || $this->client_options !== $opts)
		{
			$this->client_opts = $opts;
			$this->client = new GuzzleClient($this->client_opts);
		}

		return $this->client;
	}
}
