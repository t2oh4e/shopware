<?php

namespace Shopware\Service;

use Shopware\Struct as Struct;

/**
 * @package Shopware\Service
 */
class GlobalState
{
    private $globalStateHydrator;

    private $taxHydrator;

    private $customerGroupHydrator;

    private $currencyHydrator;

    private $session;

    function __construct(
        $currencyHydrator,
        $customerGroupHydrator,
        $globalStateHydrator,
        $taxHydrator,
        $session
    ) {
        $this->currencyHydrator = $currencyHydrator;
        $this->customerGroupHydrator = $customerGroupHydrator;
        $this->globalStateHydrator = $globalStateHydrator;
        $this->taxHydrator = $taxHydrator;
        $this->session = $session;
    }

    /**
     * @return Struct\GlobalState
     */
    public function get()
    {
        return new Struct\GlobalState();
    }
}