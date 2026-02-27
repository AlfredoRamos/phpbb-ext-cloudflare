<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\includes;

use GuzzleHttp\Exception\GuzzleException;

class cloudflare
{
	use http_trait;

	/** @var string */
	private const API_BASE_URL = 'https://api.cloudflare.com/client/v4/';

	/** @var array */
	public const PURGE_CACHE_TYPES = ['purge_everything', /*'files', 'tags',*/ 'hosts'/*, 'prefixes'*/]; // TODO: Add support for missing types

	// TODO: Improve returned data to indentify the error
	public function purge_cache(string $api_token = '', string $zone_id = '', array $opts = []): mixed
	{
		if (empty($api_token) || empty($zone_id) || empty($opts['type']) || !in_array($opts['type'], self::PURGE_CACHE_TYPES, true))
		{
			return null;
		}

		$payload = null;

		switch($opts['type'])
		{
			case 'purge_everything':
				$payload = [$opts['type'] => true]; // Override value
				break;

			// TODO: Add support for missing types
			/*
			case 'files':
				break;

			case 'tags':
				break;
			*/

			case 'hosts':
				$payload = [$opts['type'] => $opts['value']];
				break;

			// TODO: Add support for missing types
			/*
			case 'prefixes':
				break;
			*/
		}

		if (empty($payload))
		{
			return null;
		}

		try
		{
			$client = $this->get_client(['base_uri' => self::API_BASE_URL]);

			$response = $client->request('POST', sprintf('zones/%s/purge_cache', $zone_id), [
				'json' => $payload,
				'headers' => [
					'Authorization' => 'Bearer ' . $api_token
				]
			]);

			$data = json_decode($response->getBody()->getContents(), true, JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);

			return $data;
		}
		catch (GuzzleException | JsonException $ex)
		{
			// $ex->getMessage();
			return null;
		};

		unset($api_token, $zone_id);

		return null;
	}
}
