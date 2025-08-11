<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

// Globals declared in scripts
global $DB;
assertType(\DBmysql::class, $DB);

global $GLPI_CACHE, $TRANSLATE;
assertType(\Psr\SimpleCache\CacheInterface::class, $GLPI_CACHE);
assertType(\Laminas\I18n\Translator\Translator::class, $TRANSLATE);

global $PHPLOGGER, $SQLLOGGER;
assertType(\Psr\Log\LoggerInterface::class, $PHPLOGGER);
assertType(\Psr\Log\LoggerInterface::class, $SQLLOGGER);

global $ANOTHER_GLOBAL;
assertType('mixed', $ANOTHER_GLOBAL); // unresolved type fallbacks to mixed

global $CFG_GLPI;
assertType('array<string, mixed>', $CFG_GLPI);

global $PLUGIN_HOOKS;
assertType('array<string, mixed>', $PLUGIN_HOOKS);

global $_UGET, $_UPOST, $_UREQUEST, $_UFILES;
assertType('array<string, mixed>', $_UGET);
assertType('array<string, mixed>', $_UPOST);
assertType('array<string, mixed>', $_UREQUEST);
assertType('array<string, mixed>', $_UFILES);


global $AJAX_INCLUDE, $HEADER_LOADED, $FOOTER_LOADED;
assertType('bool', $AJAX_INCLUDE);
assertType('bool', $HEADER_LOADED);
assertType('bool', $FOOTER_LOADED);

// Globals declared in functions
function whatever(): void
{
    global $DB;
    assertType(\DBmysql::class, $DB);

    global $GLPI_CACHE, $TRANSLATE;
    assertType(\Psr\SimpleCache\CacheInterface::class, $GLPI_CACHE);
    assertType(\Laminas\I18n\Translator\Translator::class, $TRANSLATE);

    global $PHPLOGGER, $SQLLOGGER;
    assertType(\Psr\Log\LoggerInterface::class, $PHPLOGGER);
    assertType(\Psr\Log\LoggerInterface::class, $SQLLOGGER);

    global $FOOBAR;
    assertType('mixed', $FOOBAR); // unresolved type fallbacks to mixed

    global $CFG_GLPI;
    assertType('array<string, mixed>', $CFG_GLPI);

    global $PLUGIN_HOOKS;
    assertType('array<string, mixed>', $PLUGIN_HOOKS);

    global $_UGET, $_UPOST, $_UREQUEST, $_UFILES;
    assertType('array<string, mixed>', $_UGET);
    assertType('array<string, mixed>', $_UPOST);
    assertType('array<string, mixed>', $_UREQUEST);
    assertType('array<string, mixed>', $_UFILES);


    global $AJAX_INCLUDE, $HEADER_LOADED, $FOOTER_LOADED;
    assertType('bool', $AJAX_INCLUDE);
    assertType('bool', $HEADER_LOADED);
    assertType('bool', $FOOTER_LOADED);
}

// Globals declared in a class
class Foo
{
    public function bar(): void
    {
        global $DB;
        assertType(\DBmysql::class, $DB);

        global $GLPI_CACHE, $TRANSLATE;
        assertType(\Psr\SimpleCache\CacheInterface::class, $GLPI_CACHE);
        assertType(\Laminas\I18n\Translator\Translator::class, $TRANSLATE);

        global $PHPLOGGER, $SQLLOGGER;
        assertType(\Psr\Log\LoggerInterface::class, $PHPLOGGER);
        assertType(\Psr\Log\LoggerInterface::class, $SQLLOGGER);

        global $MYGLOB;
        assertType('mixed', $MYGLOB); // unresolved type fallbacks to mixed

        global $CFG_GLPI;
        assertType('array<string, mixed>', $CFG_GLPI);

        global $PLUGIN_HOOKS;
        assertType('array<string, mixed>', $PLUGIN_HOOKS);

        global $_UGET, $_UPOST, $_UREQUEST, $_UFILES;
        assertType('array<string, mixed>', $_UGET);
        assertType('array<string, mixed>', $_UPOST);
        assertType('array<string, mixed>', $_UREQUEST);
        assertType('array<string, mixed>', $_UFILES);


        global $AJAX_INCLUDE, $HEADER_LOADED, $FOOTER_LOADED;
        assertType('bool', $AJAX_INCLUDE);
        assertType('bool', $HEADER_LOADED);
        assertType('bool', $FOOTER_LOADED);
    }
}
