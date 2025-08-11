<?php

declare(strict_types=1);

namespace PHPStanGlpi\Analyser;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\File\FileHelper;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\ExpressionTypeResolverExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStanGlpi\Services\GlpiPathResolver;
use PHPStanGlpi\Services\GlpiVersionResolver;

final class GlobalTypeResolver implements ExpressionTypeResolverExtension
{
    private FileHelper $fileHelper;

    private GlpiPathResolver $glpiPathResolver;

    private GlpiVersionResolver $glpiVersionResolver;

    public function __construct(
        FileHelper $fileHelper,
        GlpiPathResolver $glpiPathResolver,
        GlpiVersionResolver $glpiVersionResolver
    ) {
        $this->fileHelper = $fileHelper;
        $this->glpiPathResolver = $glpiPathResolver;
        $this->glpiVersionResolver = $glpiVersionResolver;
    }

    public function getType(Expr $expr, Scope $scope): ?Type
    {
        if (!$expr instanceof Variable || !is_string($expr->name)) {
            return null;
        }

        $name = $expr->name;

        if (!$scope->hasVariableType($name)->no() && !$scope->getVariableType($name) instanceof MixedType) {
            // Variables will have the `mixed` type unless a PHPDoc block defines their type.
            // We skip variables with already defined type to not overrided those explicitely defined.
            return null;
        }

        if ($name === 'CFG_GLPI') {
            return new ArrayType(new StringType(), new MixedType());
        }

        if ($name === 'PLUGIN_HOOKS') {
            return new ArrayType(new StringType(), new MixedType());
        }

        if ($name === 'DB') {
            return new ObjectType(\DBmysql::class); // @phpstan-ignore class.notFound
        }

        if ($name === 'GLPI_CACHE') {
            return new ObjectType(\Psr\SimpleCache\CacheInterface::class); // @phpstan-ignore class.notFound
        }

        if ($name === 'PHPLOGGER' || $name === 'SQLLOGGER') {
            return new ObjectType(\Psr\Log\LoggerInterface::class);
        }

        if ($name === 'TRANSLATE') {
            return new ObjectType(\Laminas\I18n\Translator\Translator::class); // @phpstan-ignore class.notFound
        }

        $glpiVersion = $this->glpiVersionResolver->getGlpiVersion();

        if (
            \version_compare($glpiVersion, '11.0.0-dev', '<')
            && ($type = $this->getGlobalTypeForGlpi10($name))
        ) {
            return $type;
        }

        if ($type = $this->getGlobalTypeForGlpiMigrations($name, $scope)) {
            return $type;
        }

        return null;
    }

    private function getGlobalTypeForGlpiMigrations(string $name, Scope $scope): ?Type
    {
        $migrationsPath = $this->fileHelper->normalizePath($this->glpiPathResolver->getGlpiPath() . '/install/migrations');
        $filePath = $this->fileHelper->normalizePath($scope->getFile());

        if (!\str_starts_with($filePath, $migrationsPath)) {
            return null;
        }

        if ($name === 'migration') {
            return new ObjectType(\Migration::class); // @phpstan-ignore class.notFound
        }

        return null;
    }

    private function getGlobalTypeForGlpi10(string $name): ?Type
    {
        if (\in_array($name, ['AJAX_INCLUDE', 'HEADER_LOADED', 'FOOTER_LOADED'], true)) {
            return new BooleanType();
        }

        if (\in_array($name, ['_UGET', '_UPOST', '_UREQUEST', '_UFILES'], true)) {
            return new ArrayType(new StringType(), new MixedType());
        }

        return null;
    }
}
