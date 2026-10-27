<?php

namespace alfredoramos\cloudflare\tests\captcha;

use alfredoramos\cloudflare\captcha\plugins\turnstile;
use alfredoramos\cloudflare\includes\helper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class turnstile_fixture extends turnstile
{
	public $http_client;
	public $http_options;
	public $use_database = false;

	protected function get_client(?array $opts = null): Client
	{
		$this->http_options = $opts;
		return $this->http_client;
	}

	public function new_attempt()
	{
		if ($this->use_database)
		{
			parent::new_attempt();
			return;
		}

		$this->attempts++;
		$this->solved = 0;
	}
}

class turnstile_test extends \phpbb_test_case
{
	private $values;
	private $history;
	private $helper;

	protected function setUp(): void
	{
		parent::setUp();
		global $request, $user;
		$this->values = ['cf-turnstile-response' => 'fixture-token'];
		$this->history = [];
		$request = $this->getMockBuilder(\phpbb\request\request::class)->disableOriginalConstructor()->getMock();
		$request->method('variable')->willReturnCallback(function ($key, $default) {
			return $this->values[$key] ?? $default;
		});
		$user = $this->getMockBuilder(\phpbb\user::class)->disableOriginalConstructor()->getMock();
		$user->method('is_setup')->willReturn(true);
		$user->lang = ['CONFIRM_CODE_WRONG' => 'wrong'];
		$user->ip = '192.0.2.1';
		$this->helper = $this->getMockBuilder(helper::class)->disableOriginalConstructor()->getMock();
		$this->helper->method('uuid_v4')->willReturn('64027e53-0ced-40d4-b64f-aa6c33207568');
	}

	private function plugin(array $responses): turnstile_fixture
	{
		global $request, $user;
		$language = $this->getMockBuilder(\phpbb\language\language::class)->disableOriginalConstructor()->getMock();
		$language->method('lang')->willReturnArgument(0);
		$plugin = new turnstile_fixture(
			new \phpbb\config\config(['turnstile_key' => 'fixture-key', 'turnstile_secret' => 'fixture-secret']),
			$user, $request, $this->createMock(\phpbb\template\template::class), $language,
			$this->createMock(\phpbb\log\log_interface::class), $this->helper, './', 'php'
		);
		$plugin->confirm_id = 'fixture-confirm-id';
		$plugin->code = 'server-code';
		$plugin->confirm_code = '';
		$plugin->type = CONFIRM_REG;
		$stack = HandlerStack::create(new MockHandler($responses));
		$stack->push(Middleware::history($this->history));
		$plugin->http_client = new Client(['handler' => $stack]);
		return $plugin;
	}

	public function test_success_is_cached_within_request()
	{
		$plugin = $this->plugin([new Response(200, [], '{"success":true}')]);
		$this->assertFalse($plugin->validate());
		$this->assertFalse($plugin->validate());
		$this->assertCount(1, $this->history);
		$this->assertSame(['connect_timeout' => 5, 'timeout' => 10], $plugin->http_options);
	}

	public function test_missing_token_does_not_call_siteverify()
	{
		$this->values = [];
		$this->assertSame('TURNSTILE_INCORRECT', $this->plugin([])->validate());
		$this->assertCount(0, $this->history);
	}

	public static function rejected_response_provider(): array
	{
		return [
			['{"success":false,"error-codes":["timeout-or-duplicate"]}'],
			['{"success":"true"}'],
			['{}'], ['null'], ['[]'], ['true'], ['{invalid']
		];
	}

	/** @dataProvider rejected_response_provider */
	public function test_failed_or_malformed_response_is_rejected_once(string $body)
	{
		$this->helper->expects($this->never())->method('backoff_delay');
		$this->assertSame('TURNSTILE_INCORRECT', $this->plugin([new Response(200, [], $body)])->validate());
		$this->assertCount(1, $this->history);
	}

	public function test_form_correction_can_obtain_a_fresh_token()
	{
		$plugin = $this->plugin([new Response(200, [], '{"success":true}')]);
		$this->assertFalse($plugin->validate());
		$this->assertSame('@alfredoramos_cloudflare/captcha_turnstile.html', $plugin->get_template());
		$hidden = $plugin->get_hidden_fields();
		$this->assertSame('fixture-confirm-id', $hidden['confirm_id']);
		$this->assertArrayNotHasKey('confirm_code', $hidden);

		$this->values = $hidden + ['cf-turnstile-response' => 'fresh-token'];
		$next = $this->plugin([new Response(200, [], '{"success":true}')]);
		$this->assertFalse($next->validate());
		$this->assertCount(2, $this->history);
		parse_str((string) $this->history[1]['request']->getBody(), $data);
		$this->assertSame('fresh-token', $data['response']);
	}

	public function test_solved_login_keeps_existing_template_and_hidden_fields()
	{
		$plugin = $this->plugin([]);
		$plugin->type = CONFIRM_LOGIN;
		$plugin->solved = true;
		$plugin->confirm_code = 'existing-proof';
		$this->assertFalse($plugin->get_template());
		$this->assertSame('existing-proof', $plugin->get_hidden_fields()['confirm_code']);
	}

	public function test_network_failure_is_bounded_and_does_not_expose_details()
	{
		$this->helper->expects($this->exactly(2))->method('backoff_delay');
		$error = new ConnectException('private transport details', new Request('POST', 'https://example.invalid'));
		$plugin = $this->plugin([$error, $error, $error]);
		$this->assertSame('TURNSTILE_INCORRECT', $plugin->validate());
		$this->assertCount(3, $this->history);
		$keys = [];
		foreach ($this->history as $entry)
		{
			parse_str((string) $entry['request']->getBody(), $data);
			$keys[] = $data['idempotency_key'];
		}
		$this->assertCount(1, array_unique($keys));
	}

	public function test_network_retry_can_recover()
	{
		$this->helper->expects($this->once())->method('backoff_delay');
		$error = new ConnectException('fixture', new Request('POST', 'https://example.invalid'));
		$plugin = $this->plugin([$error, new Response(200, [], '{"success":true}')]);
		$this->assertFalse($plugin->validate());
		$this->assertCount(2, $this->history);
	}

	public function test_registration_correction_with_session_confirm_records()
	{
		if (!extension_loaded('sqlite3'))
		{
			$this->markTestSkipped('SQLite3 is required for the confirmation-record lifecycle test.');
		}

		global $db, $config, $user;
		$db = new \phpbb\db\driver\sqlite3();
		$db->sql_connect(':memory:', '', '', '');
		$db->sql_query('CREATE TABLE ' . CONFIRM_TABLE . ' (confirm_id TEXT PRIMARY KEY, session_id TEXT, confirm_type INTEGER, code TEXT, seed INTEGER, attempts INTEGER DEFAULT 0)');
		$config = new \phpbb\config\config(['confirm_refresh' => 0]);
		$user->session_id = 'fixture-session';

		try
		{
			$this->values = [];
			$first = $this->plugin([]);
			$first->use_database = true;
			$first->init(CONFIRM_REG);
			$hidden = $first->get_hidden_fields();
			$this->values = $hidden + ['cf-turnstile-response' => 'first-token'];
			$submitted = $this->plugin([new Response(200, [], '{"success":true}')]);
			$submitted->use_database = true;
			$submitted->init(CONFIRM_REG);
			$this->assertFalse($submitted->validate());
			$this->assertSame('@alfredoramos_cloudflare/captcha_turnstile.html', $submitted->get_template());

			$this->values = $submitted->get_hidden_fields() + ['cf-turnstile-response' => 'corrected-form-token'];
			$corrected = $this->plugin([new Response(200, [], '{"success":true}')]);
			$corrected->use_database = true;
			$corrected->init(CONFIRM_REG);
			$this->assertFalse($corrected->validate());
			$this->assertCount(2, $this->history);
			$corrected->reset();
			$result = $db->sql_query("SELECT COUNT(*) AS total FROM " . CONFIRM_TABLE . " WHERE confirm_id = '" . $db->sql_escape($hidden['confirm_id']) . "'");
			$this->assertSame(0, (int) $db->sql_fetchfield('total', false, $result));
			$db->sql_freeresult($result);
		}
		finally
		{
			$db->sql_close();
			unset($GLOBALS['db'], $GLOBALS['config']);
		}
	}
}
