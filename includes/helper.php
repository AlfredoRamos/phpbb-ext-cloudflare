<?php

/**
 * Cloudflare extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2021 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\cloudflare\includes;

use phpbb\db\driver\factory as database;
use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\request\request;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\content_visibility;
use phpbb\routing\helper as routing_helper;
use alfredoramos\cloudflare\includes\cloudflare as cloudflare_client;

class helper
{
	/** @var database */
	protected $db;

	/** @var auth */
	protected $auth;

	/** @var ?array */
	private $guest = null;

	/** @var config */
	protected $config;

	/** @var request */
	protected $request;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/** @var content_visibility */
	protected $content_visibility;

	/** @var routing_helper */
	protected $routing_helper;

	/** @var cloudflare_client */
	protected $cloudflare_client;

	/** @var array */
	protected $tables = [];

	/** @var integer */
	private const MAX_CACHE_TAGS = 5;

	/** @var integer */
	public const MIN_CACHE_TIME = 1;

	/** @var integer */
	public const MAX_CACHE_TIME = 365;

	/** @var array */
	public const SUPPORTED_CACHE_UNITS = ['h', 'd'];

	/**
	 * Helper constructor.
	 *
	 * @param database				$db
	 * @param auth					$auth
	 * @param config				$config
	 * @param request				$request
	 * @param language				$language
	 * @param template				$template
	 * @param content_visibility	$content_visibility
	 * @param routing_helper		$routing_helper
	 * @param cloudflare_client		$cloudflare_client
	 * @param string				$users_table
	 * @param string				$posts_table
	 *
	 * @param void
	 */
	public function __construct(database $db, auth $auth, config $config, request $request, language $language, template $template, content_visibility $content_visibility, routing_helper $routing_helper, cloudflare_client $cloudflare_client, string $users_table, string $posts_table)
	{
		$this->db = $db;
		$this->auth = $auth;
		$this->config = $config;
		$this->request = $request;
		$this->language = $language;
		$this->template = $template;
		$this->content_visibility = $content_visibility;
		$this->routing_helper = $routing_helper;
		$this->cloudflare_client = $cloudflare_client;

		// Assign tables
		if (empty($this->tables))
		{
			$this->tables = [
				'users' => $users_table,
				'posts' => $posts_table,
			];
		}
	}

	/**
	 * Validate form fields with given filters.
	 *
	 * @param array $fields		Pair of field name and value
	 * @param array $filters	Filters that will be passed to filter_var_array()
	 * @param array $errors		Array of message errors
	 *
	 * @return bool
	 */
	public function validate(array &$fields = [], array &$filters = [], array &$errors = []): bool
	{
		if (empty($fields) || empty($filters))
		{
			return false;
		}

		// Filter fields
		$data = filter_var_array($fields, $filters, false);

		// Invalid fields helper
		$invalid = [];

		// Validate fields
		foreach ($data as $key => $value)
		{
			// Remove and generate error if field did not pass validation
			// Not using empty() because an empty string can be a valid value
			if (!isset($value) || $value === false)
			{
				$invalid[] = $this->language->lang(sprintf('%s', strtoupper($key)));
				unset($fields[$key]);
			}
		}

		if (!empty($invalid))
		{
			$errors[]['message'] = $this->language->lang(
				'ACP_CLOUDFLARE_VALIDATE_INVALID_FIELDS',
				implode($this->language->lang('COMMA_SEPARATOR'), $invalid)
			);
		}

		// Validation check
		return empty($errors);
	}

	public function guest_user(): array
	{
		if ($this->guest === null)
		{
			$sql = 'SELECT user_id, user_permissions, user_type
				FROM ' . $this->tables['users'] . '
				WHERE ' . $this->db->sql_build_array('SELECT', ['user_id' => ANONYMOUS]);

			// Cache query for 7 days
			$result = $this->db->sql_query($sql, (7 * 24 * 60 * 60));
			$guest = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!empty($guest))
			{
				$this->guest = $guest;
			}
		}

		return $this->guest;
	}

	public function attachment_forum_id(int $post_msg_id = 0): int
	{
		if (empty($post_msg_id))
		{
			return 0;
		}

		$sql = 'SELECT forum_id, poster_id, post_visibility
			FROM ' . $this->tables['posts'] . '
			WHERE ' . $this->db->sql_build_array('SELECT', ['post_id' => $post_msg_id]);

		// Cache query for 1 hour
		$result = $this->db->sql_query($sql, (60 * 60));
		$post_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// Soft-deleted post
		if (!$post_row || !$this->content_visibility->is_visible('post', $post_row['forum_id'], $post_row))
		{
			return 0;
		}

		return (int) $post_row['forum_id'];
	}

	public function auth_guest(): auth
	{
		$auth_guest = clone $this->auth;
		$auth_guest->acl($this->guest_user());

		return $auth_guest;
	}

	public function is_public_forum(int $forum_id = 0): bool
	{
		if (empty($forum_id))
		{
			return false;
		}

		return (bool) $this->auth_guest()->acl_get('f_read', $forum_id) &&
			(bool) $this->auth_guest()->acl_get('f_download', $forum_id);
	}

	public function is_public_attachment(array $attachment = []): bool
	{
		if (empty($attachment) || !empty($attachment['is_orphan']))
		{
			return false;
		}

		$forum_id = $this->attachment_forum_id((int) $attachment['post_msg_id']);

		return $this->is_public_forum($forum_id) && $this->can_download();
	}

	public function can_download(): bool
	{
		return (bool) $this->auth_guest()->acl_get('u_download');
	}

	public function original_visitor_ip(): string|null
	{
		$ip = null;

		if (!empty($this->request->server('HTTP_CF_CONNECTING_IP')))
		{
			$ip = htmlspecialchars_decode($this->request->server('HTTP_CF_CONNECTING_IP'));
		}

		return $ip;
	}

	public function cache_time(int $quantity = 7, string $unit = 'd'): int
	{
		// Boundary checks
		$quantity = abs($quantity);
		$quantity = ($quantity == 0) ? 7 : $quantity; // Default value
		$quantity = max(self::MIN_CACHE_TIME, $quantity);
		$quantity = min(self::MAX_CACHE_TIME, $quantity);

		$unit = (empty($unit) || !in_array($unit, self::SUPPORTED_CACHE_UNITS)) ? self::SUPPORTED_CACHE_UNITS[1] : $unit;

		$cache_time = 0;
		$seconds = 60 * 60;

		switch($unit)
		{
			case 'h': // Hours to seconds
				$cache_time = $quantity * $seconds;
				break;

			case 'd': // Days to seconds
				$cache_time = $quantity * 24 * $seconds;
				break;
		}

		return $cache_time;
	}

	public function cache_headers(array $data = [], array $tags = []): void
	{
		if (empty($data) || !$this->is_public_attachment($data))
		{
			return;
		}

		if (count($tags) > self::MAX_CACHE_TAGS)
		{
			$tags = array_slice($tags, 0, self::MAX_CACHE_TAGS);
		}

		$tags = $this->sanitize_string_list($tags);

		$cache_time = $this->cache_time(
			(int) $this->config->offsetGet('cloudflare_cache_time'),
			trim($this->config->offsetGet('cloudflare_cache_type'))
		);

		if (empty($cache_time))
		{
			return;
		}

		$etag = hash('xxh3', $data['attach_id'] . $data['physical_filename'] . ['filesize']);

		header(sprintf('Cache-Control: public, max-age=%d', $cache_time));
		header(sprintf('Expires: %s GMT', gmdate('D, d M Y H:i:s', time() + $cache_time)));
		header(sprintf('ETag: "%s"', $etag));

		if (count($tags) > 0)
		{
			header(sprintf('Cache-Tag: %s', implode(',', $tags)));
		}

		if (!empty($this->request->server('HTTP_IF_NONE_MATCH')) && trim($this->request->server('HTTP_IF_NONE_MATCH')) === $etag)
		{
			send_status_line(304, 'Not Modified');
			exit;
		}
	}

	public function sanitize_string_list(array $list = []): array
	{
		$ary = [];

		foreach ($list as $item)
		{
			if (!is_string($item))
			{
				continue;
			}

			$item = filter_var(trim($item), FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_EMPTY_STRING_NULL|FILTER_FLAG_STRIP_BACKTICK);

			if (empty($item) || in_array($item, $ary))
			{
				continue;
			}

			$ary[] = $item;
		}

		return $ary;
	}

	public function uuid_v4(): string
	{
		$data = random_bytes(16);

		// Set version to 0100
		$data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

		// Set variant to 10
		$data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	// * delay = base_delay × 2^(attempt − 1) + jitter
	public function backoff_delay(int $attempt, int $base_ms = 250, int $max_ms = 2500): void {
		// Exponential backoff
		$delay_ms = min($base_ms * (2 ** ($attempt - 1)), $max_ms);

		// Add jitter (±25%)
		$jitter = random_int((int) ($delay_ms * -0.25), (int) ($delay_ms *  0.25));

		usleep(($delay_ms + $jitter) * 1000);
	}

	public function acp_assign_template_variables()
	{
		$this->language->add_lang(['acp/settings'], 'alfredoramos/cloudflare');

		$this->template->assign_vars([
			'BOARD_URL' => generate_board_url(),
			'CLOUDFLARE_PURGE_CACHE_URL' => $this->routing_helper->route('alfredoramos_cloudflare_purge_cache', [
				'hash' => generate_link_hash('cloudflare_purge_cache')
			])
		]);

		foreach ($this->cloudflare_client::PURGE_CACHE_TYPES as $key => $value)
		{
			$this->template->assign_block_vars('CLOUDFLARE_PURGE_CACHE_TYPES', [
				'NAME' => $this->language->lang(sprintf('CLOUDFLARE_PURGE_CACHE_TYPE_%s', strtoupper($value))),
				'VALUE' => $value,
			]);
		}
	}
}
