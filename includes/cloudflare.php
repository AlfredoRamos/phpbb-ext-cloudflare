<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2026 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\includes;

use GuzzleHttp\Exception\GuzzleException;

// TODO: Improve returned data to indentify errors
class cloudflare
{
	use http_trait;

	/** @var string */
	private $api_token = null;

	/** @var string */
	private $zone_id = null;

	/** @var string */
	private const API_BASE_URL = 'https://api.cloudflare.com/client/v4/';

	/** @var array */
	public const PURGE_CACHE_TYPES = ['purge_everything', /*'files', 'tags',*/ 'hosts'/*, 'prefixes'*/]; // TODO: Add support for missing types

	/** @var array */
	public const RULESET_KINDS = ['zone'];

	/** @var array */
	public const RULESET_PHASES = ['http_request_cache_settings', 'http_request_firewall_custom'];

	/** @var array */
	public const CHALLENGE_TYPES = ['managed_challenge', 'challenge', 'js_challenge'];

	public function __construct(?string $api_token = null, ?string $zone_id = null)
	{
		if (!empty($api_token))
		{
			$this->api_token = $api_token;
		}

		if (!empty($zone_id))
		{
			$this->zone_id = $zone_id;
		}

		$this->get_client(['base_uri' => self::API_BASE_URL]);
	}

	public function set_options(array $opts = []): void
	{
		if (empty($opts))
		{
			return;
		}

		if (count($opts) > 2)
		{
			$opts = array_slice($opts, 0, 2, true);
		}

		$allowed = ['api_token', 'zone_id'];

		foreach($opts as $key => $value)
		{
			if (empty($key) || !is_string($key) || !in_array($key, $allowed, true) || empty($value) || !is_string($value))
			{
				continue;
			}

			switch($key)
			{
				case 'api_token':
					$this->api_token = $value;
					break;

				case 'zone_id':
					$this->zone_id = $value;
					break;
			}
		}
	}

	protected function make_request(string $method = 'GET', string $endpoint = '', ?array $payload = null): ?array
	{
		$allowed = ['method' => ['GET', 'POST', 'PUT', 'PATCH']];

		if (empty($method) || !in_array($method, $allowed['method'], true) || empty($endpoint))
		{
			return null;
		}

		$params = [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->api_token
			]
		];

		if ($method !== 'GET' && !empty($payload))
		{
			$params['json'] = $payload ?? [];
		}
		else
		{
			unset($params['json']);
		}

		try
		{
			$response = $this->client->request($method, $endpoint, $params);
			$result = json_decode($response->getBody()->getContents(), true, JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);

			return $result;
		}
		catch (GuzzleException | JsonException $ex)
		{
			return null;
		};
	}

	public function purge_cache(array $opts = []): ?array
	{
		if (empty($this->api_token) || empty($this->zone_id) || empty($opts['type']) || !in_array($opts['type'], self::PURGE_CACHE_TYPES, true))
		{
			return null;
		}

		$payload = [];

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

		return $this->make_request('POST', sprintf('zones/%s/purge_cache', $this->zone_id), $payload);
	}

	public function find_ruleset(array $opts = [], bool $match_all = false): ?array
	{
		if (empty($this->api_token) || empty($this->zone_id) || empty($opts))
		{
			return null;
		}

		$rulesets = $this->get_rulesets();

		if (empty($rulesets['result']) || !is_array($rulesets['result']))
		{
			return null;
		}

		$allowed_fields = ['kind', 'phase'];
		$filteded_fields = array_flip($allowed_fields);
		$filtered = array_intersect_key($opts, $filteded_fields);

		if (empty($filtered))
		{
			return null;
		}

		foreach($rulesets['result'] as $ruleset)
		{
			if (!is_array($ruleset) || !array_intersect_key($ruleset, $filteded_fields))
			{
				continue;
			}

			$matches = 0;

			foreach($filtered as $key => $value)
			{
				if (!empty($ruleset[$key]) && $ruleset[$key] === $value)
				{
					$matches++;
				}

				if (($match_all && $matches === count($filtered)) || (!$match_all && $matches > 0))
				{
					return $ruleset;
				}
			}
		}

		return null;
	}

	public function find_ruleset_rules(string $ruleset_id = null, array $opts = [], bool $match_all = false): array
	{
		if (empty($this->api_token) || empty($this->zone_id) || empty($ruleset_id) || empty($opts))
		{
			return [];
		}

		$ruleset_info = $this->get_ruleset($ruleset_id);

		if (empty($ruleset_info) || empty($ruleset_info['rules']))
		{
			return [];
		}

		$allowed_fields = ['action', 'description'];
		$filteded_fields = array_flip($allowed_fields);
		$filtered = array_intersect_key($opts, $filteded_fields);

		if (empty($filtered))
		{
			return [];
		}

		foreach($ruleset_info['rules'] as $rule)
		{
			if (!is_array($rule) || !array_intersect_key($rule, $filteded_fields))
			{
				continue;
			}

			$matches = 0;

			foreach($filtered as $key => $value)
			{
				if (!empty($rule[$key]) && $rule[$key] === $value)
				{
					$matches++;
				}

				if (($match_all && $matches === count($filtered)) || (!$match_all && $matches > 0))
				{
					return $rule;
				}
			}
		}

		return [];
	}

	public function get_rulesets(): ?array
	{
		if (empty($this->api_token) || empty($this->zone_id))
		{
			return null;
		}

		return $this->make_request('GET', sprintf('zones/%s/rulesets', $this->zone_id));
	}

	public function get_ruleset(string $ruleset_id = ''): ?array
	{
		if (empty($this->api_token) || empty($this->zone_id) || empty($ruleset_id))
		{
			return null;
		}

		return $this->make_request('GET', sprintf('zones/%s/rulesets/%s', $this->zone_id, $ruleset_id));
	}

	public function create_ruleset(array $data = []): ?array
	{
		if (empty($this->api_token) || empty($this->zone_id) || empty($data))
		{
			return null;
		}

		$required = ['name', 'description', 'kind', 'phase'];
		$allowed = [
			'kind' => self::RULESET_KINDS,
			'phase' => self::RULESET_PHASES
		];
		$payload = [];
		$missing = [];

		foreach ($data as $key => $value)
		{
			switch($key)
			{
				case 'kind':
				case 'phase':
					if (empty($value) || !in_array($value, $allowed[$key], true))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;

				default:
					if (empty($value))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;
			}
		}

		if (!empty($missing) || empty($payload))
		{
			return null;
		}

		return $this->make_request('POST', sprintf('zones/%s/rulesets', $this->zone_id), $payload);
	}

	public function create_ruleset_rules(string $ruleset_id = '', array $data = []): ?array
	{

		if (empty($this->api_token) || empty($this->zone_id) || empty($ruleset_id) || empty($data))
		{
			return null;
		}

		$required = ['description', 'expression', 'action'];
		$payload = [];
		$missing = [];

		foreach ($data as $key => $value)
		{
			switch($key)
			{
				case 'action':
					if (empty($value) || !in_array($value, self::CHALLENGE_TYPES, true))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;

				case 'position':
					if (empty($value) || !is_array($value))
					{
						continue 2;
					}

					if ($value <= 0)
					{
						continue 2;
					}

					$payload[$key] = $value;

					if (!empty($payload[$key]['index']))
					{
						$payload[$key]['index'] = (int) $payload[$key]['index'];
					}
					break;

				case 'action_parameters':
					if (empty($value) || !is_array($value))
					{
						continue 2;
					}

					$payload[$key] = $value;
					break;

				default:
					if (empty($value))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;
			}
		}

		if (!empty($missing) || empty($payload))
		{
			return null;
		}

		return $this->make_request('POST', sprintf('zones/%s/rulesets/%s/rules', $this->zone_id, $ruleset_id), $payload);
	}

	public function update_ruleset(string $ruleset_id = '', array $data = []): ?array
	{
		if (empty($this->api_token) || empty($this->zone_id) || empty($ruleset_id) || empty($data))
		{
			return null;
		}

		$required = ['kind', 'phase'];
		$payload = [];
		$missing = [];

		// TODO: Validate required values
		foreach ($data as $key => $value)
		{
			$value = trim($value);

			switch($key)
			{
				case 'kind':
					if (empty($value) || !in_array($value, self::RULESET_KINDS, true))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;

				case 'phase':
					if (empty($value) || !in_array($value, self::RULESET_PHASES, true))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;

				default:
					if (empty($value))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;
			}
		}

		if (!empty($missing) || empty($payload))
		{
			return null;
		}

		return $this->make_request('PUT', sprintf('zones/%s/rulesets/%s', $this->zone_id, $ruleset_id), $payload);
	}

	public function update_ruleset_rules(string $ruleset_id = '', string $rule_id = '', array $data = []): ?array
	{
		if (empty($this->api_token) || empty($this->zone_id) || empty($ruleset_id) || empty($rule_id) || empty($data))
		{
			return null;
		}

		$required = ['description', 'expression', 'action'];
		$payload = [];
		$missing = [];

		// TODO: Validate required values
		foreach ($data as $key => $value)
		{
			$value = trim($value);

			switch($key)
			{
				case 'action':
					if (empty($value) || !in_array($value, self::CHALLENGE_TYPES, true))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;

				default:
					if (empty($value))
					{
						$missing[] = $key;
						continue 2;
					}

					$payload[$key] = $value;
					break;
			}
		}

		if (!empty($missing) || empty($payload))
		{
			return null;
		}

		return $this->make_request('PATCH', sprintf('zones/%s/rulesets/%s/rules/%s', $this->zone_id, $ruleset_id, $rule_id), $payload);
	}
}
