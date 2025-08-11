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

global $_UGET, $_UPOST, $_UREQUEST, $_UFILES; // unresolved anymore in GLPI 11.0
assertType('mixed', $_UGET);
assertType('mixed', $_UPOST);
assertType('mixed', $_UREQUEST);
assertType('mixed', $_UFILES);


global $AJAX_INCLUDE, $HEADER_LOADED, $FOOTER_LOADED; // unresolved anymore in GLPI 11.0
assertType('mixed', $AJAX_INCLUDE);
assertType('mixed', $HEADER_LOADED);
assertType('mixed', $FOOTER_LOADED);

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

    global $_UGET, $_UPOST, $_UREQUEST, $_UFILES; // unresolved anymore in GLPI 11.0
    assertType('mixed', $_UGET);
    assertType('mixed', $_UPOST);
    assertType('mixed', $_UREQUEST);
    assertType('mixed', $_UFILES);


    global $AJAX_INCLUDE, $HEADER_LOADED, $FOOTER_LOADED; // unresolved anymore in GLPI 11.0
    assertType('mixed', $AJAX_INCLUDE);
    assertType('mixed', $HEADER_LOADED);
    assertType('mixed', $FOOTER_LOADED);
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

        global $_UGET, $_UPOST, $_UREQUEST, $_UFILES; // unresolved anymore in GLPI 11.0
        assertType('mixed', $_UGET);
        assertType('mixed', $_UPOST);
        assertType('mixed', $_UREQUEST);
        assertType('mixed', $_UFILES);


        global $AJAX_INCLUDE, $HEADER_LOADED, $FOOTER_LOADED; // unresolved anymore in GLPI 11.0
        assertType('mixed', $AJAX_INCLUDE);
        assertType('mixed', $HEADER_LOADED);
        assertType('mixed', $FOOTER_LOADED);
    }
}
