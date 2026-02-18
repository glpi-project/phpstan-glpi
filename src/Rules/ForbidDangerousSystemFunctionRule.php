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
			'proc_open',
			'proc_close',
			'proc_nice',
			'proc_terminate',
			'dl',
			'link',
			'highlight_file',
			'show_source',
			'diskfreespace',
			'disk_free_space',
			'getmyuid',
			'popen',
			'escapeshellcmd',
			'symlink',
			'shell_exec',
			'exec',
			'system',
			'passthru',
		]);

		return $functions;
	}
}
