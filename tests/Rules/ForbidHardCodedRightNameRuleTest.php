<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidHardCodedRightNameRule;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<ForbidHardCodedRightNameRule>
 */
class ForbidHardCodedRightNameRuleTest extends RuleTestCase
{
    use TestTrait;

    protected function getRule(): Rule
    {
        return new ForbidHardCodedRightNameRule(
            $this->getGlpiVersionResolver('12.0.0')
        );
    }

    public function testSessionMethods(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidHardCodedRightNameRule/sessionMethods.php'], [
            // harcoded strings
            [
                'Hardcoded string \'hardcoded\' used as right name in Session::checkRight(). Use a class static property reference such as Hardcoded::$rightname instead.',
                6,
            ],
            [
                'Hardcoded string \'reservation\' used as right name in Session::checkRightsOr(). Use a class static property reference such as Reservation::$rightname instead.',
                7,
            ],
            [
                'Hardcoded string \'change\' used as right name in Session::haveRight(). Use a class static property reference such as Change::$rightname instead.',
                8,
            ],
            [
                'Hardcoded string \'ticketvalidation\' used as right name in Session::haveRightsOr(). Use a class static property reference such as Ticketvalidation::$rightname instead.',
                9,
            ],
            [
                'Hardcoded string \'reservation\' used as right name in Session::haveRightsAnd(). Use a class static property reference such as Reservation::$rightname instead.',
                10,
            ],
            //
        ]);
    }

}
