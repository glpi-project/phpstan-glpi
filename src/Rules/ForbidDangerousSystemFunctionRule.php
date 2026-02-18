<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Php8StubsMap;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStanGlpi\Services\GlpiVersionResolver;

/**
 * @implements Rule<FuncCall>
 */
class ForbidDangerousSystemFunctionRule implements Rule
{
    private GlpiVersionResolver $glpiVersionResolver;

    public function __construct(GlpiVersionResolver $glpiVersionResolver)
    {
        $this->glpiVersionResolver = $glpiVersionResolver;
    }

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (\version_compare($this->glpiVersionResolver->getGlpiVersion(), '11.0.0-dev', '<')) {
            // Only applies for GLPI >= 11.0.0
            return [];
        }

		$errors = [];
		foreach ($this->getForbiddenFunctions() as $forbiddenFunction) {
			if (
				$node->name instanceof Name
				&& $node->name->toString() === $forbiddenFunction
			) {
				$errors[] = RuleErrorBuilder::message(
					"You should not use the `$forbiddenFunction` function to avoid any dangerous system function call.",
				)->identifier('glpi.forbidDangerousSystemFunction')->build();
			}
		}

        return $errors;
    }

	private function getForbiddenFunctions(): array
	{
		$stubs_map = new Php8StubsMap(PHP_VERSION_ID);
		$stubs_functions = array_keys($stubs_map->functions);

		$functions = preg_grep('/^(posix_|pcntl_)/', $stubs_functions);
		// We allow some functions
		$allowed_functions = [
			'posix_geteuid',

			'pcntl_async_signals',
			'pcntl_signal',
			'pcntl_signal_get_handler',
			'pcntl_signal_dispatch',
		];
		$functions = array_diff($functions, $allowed_functions);

		// Banning other functions
		$functions = array_merge($functions, [
			'disk_free_space',
			'diskfreespace',
			'dl',
			'escapeshellcmd',
			'exec',
			'getmyuid',
			'highlight_file',
			'link',
			'passthru',
			'popen',

			'proc_close',
			'proc_nice',
			'proc_open',
			'proc_terminate',

			'shell_exec',
			'show_source',

			// We don't use stub since we only block a few functions
			'socket_accept',
			'socket_bind',
			'socket_clear_error',
			'socket_close',
			'socket_connect',
			'socket_create_listen',
			'socket_create_pair',
			'socket_listen',
			'socket_read',

			'symlink',
			'system',
		]);

		return $functions;
	}
}
