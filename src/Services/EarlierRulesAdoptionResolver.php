<?php

declare(strict_types=1);

namespace PHPStanGlpi\Services;

class EarlierRulesAdoptionResolver
{
    private GlpiVersionResolver $glpiVersionResolver;

    private bool $enabled;

    public function __construct(GlpiVersionResolver $glpiVersionResolver, bool $enabled)
    {
        $this->glpiVersionResolver = $glpiVersionResolver;
        $this->enabled = $enabled;
    }

    /**
     * Indicates whether a rule that is not enforced yet has to be applied.
     *
     * A rule that is not enforced by the detected GLPI version can still be applied, on an opt-in
     * basis, as soon as the recommended alternatives are available, thanks to the
     * `glpi.enableEarlierRulesAdoption` parameter.
     *
     * @param string $enforcedSince  Version from which the rule is unconditionally applied.
     * @param string $availableSince Version from which the rule makes sense, i.e. the version that
     *                               made the recommended alternatives available.
     *
     * @throws \LogicException
     */
    public function isRuleEnabled(string $enforcedSince, string $availableSince): bool
    {
        $version = $this->glpiVersionResolver->getGlpiVersion();

        if (\version_compare($version, $availableSince, '<')) {
            // The recommended alternatives do not exist yet, the rule cannot be adopted.
            return false;
        }

        return $this->enabled || \version_compare($version, $enforcedSince, '>=');
    }
}
