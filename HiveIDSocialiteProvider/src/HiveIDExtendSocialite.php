<?php

namespace BioHiveTech\HiveIDSocialiteProvider;

use SocialiteProviders\Manager\SocialiteWasCalled;

class HiveIDExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('hiveid', __NAMESPACE__.'\Provider');
    }
}
