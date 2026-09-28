# PHPStan GLPI extension

This repository provides a PHPStan extension that can be used in both GLPI and GLPI plugins.

## Installation

To install this PHPStan extension, run the `composer require --dev glpi-project/phpstan-glpi`.

To make this extension automatically enabled by PHPStan, you can also install the `phpstan/extension-installer` library,
otherwise you will need to add it in you PHPStan configuration file in the `includes` section:
```neon
includes:
	- vendor/glpi-project/phpstan-glpi/rules.neon
```
See https://phpstan.org/user-guide/extension-library#installing-extensions for more information.

## Configuration

The PHPStan configuration depends on your GLPI version.
If your plugin is located in either the `plugins` or `marketplace` directory of GLPI,
you can use the following configuration file example:

```neon
parameters:
    level: 0
    paths:
        - ajax
        - front
        - inc # or `src` if your PHP class files are in the `src` directory
        - hook.php
        - setup.php
    scanDirectories:
        - ../../inc
        - ../../src

    bootstrapFiles:
        - ../../stubs/glpi_constants.php
        - ../../vendor/autoload.php
```

The GLPI path and version should be detected automatically, but you can specify them in the `parameters` section of your PHPStan configuration:
```neon
parameters:
    glpi:
        glpiPath: "/path/to/glpi"
        glpiVersion: "11.0.0"
```

Some rules are not enforced yet by the detected GLPI version, but their recommended alternatives are already available.
They can be adopted in advance, to ease the migration to the next GLPI major version, using the
`enableEarlierRulesAdoption` parameter:
```neon
parameters:
    glpi:
        enableEarlierRulesAdoption: true
```

Be aware that a rule that is a candidate for earlier adoption is not stabilized yet: as long as the target GLPI version
that enables it by default is not published, it may still be changed, or even removed.

See https://phpstan.org/config-reference fore more information about the PHPStan configuration options.

## Analyser improvements

This extension will help PHPStan to resolve the GLPI global variables types.
For instance, it will indicate that the `global $DB;` variable is an instance of the `DBmysql` class,
so PHPStan will be able to detected bad method calls, deprecated methods usages, ...

## Rules

### `ForbidDynamicInstantiationRule`

> Since GLPI 11.0.

Instantiating an object from an unrestricted dynamic string is unsecure.
Indeed, it can lead to unexpected code execution and has already been a source of security issues in GLPI.

Before instantiating an object, a check must be done to validate that the variable contains an expected class string.
```php
$class = $_GET['itemtype'];

$object = new $class(); // unsafe

if (is_a($class, CommonDBTM::class, true)) {
    $object = new $class(); // safe
}
```

If the `treatPhpDocTypesAsCertain` PHPStan parameter is not set to `false`, a variable with a specific `class-string`
type will be considered safe.
```php
class MyClass
{
    /**
     * @var class-string<\CommonDBTM> $class
     */
    public function doSomething(string $class): void
    {
        $object = new $class(); // safe

        // ...
    }
}
```

### `ForbidExitRule`

> Since GLPI 11.0.

Since the introduction of the Symfony framework in GLPI 11.0, the usage of `exit()`/`die()` instructions is discouraged.
Indeed, they prevents the execution of post-request/post-command routines, and this can result in unexpected behaviours.

### `ForbidHttpResponseCodeRule`

> Since GLPI 11.0.

Due to a PHP bug (see https://bugs.php.net/bug.php?id=81451), the usage of the `http_response_code()` function, to
define the response code, may produce unexpected results, depending on the server environment.
Therefore, its usage is discouraged.

### `ForbidHardCodedRightNameRule`

> Since GLPI 12.0.

In the past, there have been issues where rights management was not handled correctly at the controller level due to the use of an obsolete hardcoded string. To avoid this type of problem, starting with GLPI 12, the use of a hardcoded string, as the first argument (`module`) of `Session::checkRight()`, `Session::checkRightsOr()`,
`Session::haveRight()`, `Session::haveRightsAnd()`, or `Session::haveRightsOr()`, will be considered an error and must be replaced with the `$rightname` property of the appropriate class.

```php
Session::checkRight('computer', READ); // wrong

Session::checkRight(Computer::$rightname, READ); // correct
```

### `ForbidNonLiteralSqlExpressionRule`

> Enforced since GLPI 13.0. Can be adopted since GLPI 12.0, using the `enableEarlierRulesAdoption` parameter.

`QueryExpression` usage is legitimate for a hardcoded fragment, but as soon as the fragment is
assembled at runtime, the safety of the whole statement depends on the caller, and nothing can verify it.
Therefore, its first argument (`expression`) must be a literal SQL string, or another query element.

```php
new QueryExpression(new QueryIdentifier('glpi_tickets.id')); // correct
new QueryExpression('COUNT(`glpi_tickets`.`id`)'); // correct

new QueryExpression(sprintf('COUNT(`%s`.`id`)', $table)); // wrong
```

Identifiers belong in `QueryIdentifier`, values in `QueryValue`, and SQL fragments in `QueryFunction` / `QuerySubQuery`.
Dynamic values can also be passed through the `values` argument, to be bound as statement parameters.

```php
new QueryExpression('DATE_ADD(`date`, INTERVAL ? DAY)', values: [$delay]); // correct
```

A call whose arguments are unpacked from an array (`new QueryExpression(...$args)`) is reported too,
since the `expression` argument cannot be located: an explicit ignore is preferred over an unreported
potential issue. Passing the `expression` argument explicitly is enough to silence it.

A literal that is nothing but an identifier reference is also reported, under the
`glpi.forbidSqlExpressionIdentifier` error identifier, as it must be built with a `QueryIdentifier`.

```php
new QueryIdentifier('glpi_tickets.id'); // correct

new QueryExpression('`glpi_tickets`.`id`'); // wrong
```

If the `treatPhpDocTypesAsCertain` PHPStan parameter is not set to `false`, a variable having a `QueryExpression` type
declared in its PHPDoc will be considered safe.

### `MissingGlobalVarTypeRule`

> Since GLPI 10.0.

By default, PHPStan is not able to detect the global variables types, and is therefore not able to detect any issue
related to their usage. This extension will resolve the type of GLPI global variables, but cannot resolve your plugin
specific global variables.
To get around this limitation, we recommend that you declare each global variable type with a PHPDoc tag.
```php
/** @var \Migration $migration */
global migration;
```
