# PHPStan GLPI extension

This repository provides a PHPStan extension that can be used in both GLPI and GLPI plugins.

## Installation

To install this PHPStan extension, run the `composer require --dev glpi-project/phpstan-glpi`.

The GLPI version should be detected automatically, but you can specify it in your PHPStan configuration:
```yaml
parameters:
    glpi:
        glpiVersion: "11.0"
```

## Rules

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
