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

The GLPI version should be detected automatically, but you can specify it in your PHPStan configuration:
```yaml
parameters:
    glpi:
        glpiVersion: "11.0"
```

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

### `MissingGlobalVarTypeRule`

> Since GLPI 10.0.

By default, PHPStan is not able to detect the global variables types, and is therefore not able to detect any issue
related to their usage. To get around this limitation, we recommend that you declare each global variable type with
a PHPDoc tag.
```php
/** @var \DBmysql $DB */
global $DB;
```
