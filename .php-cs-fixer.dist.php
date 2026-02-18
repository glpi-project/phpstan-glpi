<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
	->exclude('tests/data')
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,
        '@PHP84Migration' => true,
        'declare_strict_types' => true,
        'no_unused_imports' => true
    ])
    ->setFinder($finder)
;
