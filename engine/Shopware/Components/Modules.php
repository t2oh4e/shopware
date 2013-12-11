<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * @category  Shopware
 * @package   Shopware\Components\Core
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Components_Modules extends Enlight_Class implements ArrayAccess
{
    /**
     * Name of system class
     * @var string
     */
    protected $system;

    /**
     * Container that hold references to all modules already loaded
     * @var array
     */
    protected $modules_container = array();

    /**
     * Initiate class parameters
     * @deprecated 4.2
     * @return void
     */
    public function init()
    {
    }

    /**
     * Set class property
     * @param $system
     */
    public function setSystem($system)
    {
        $this->system = $system;
    }

    /**
     * Load a module defined by $name
     * Possible values for $name - sBasket, sAdmin etc.
     * @param $name
     */
    public function loadModule($name)
    {
        if (!isset($this->modules_container[$name])) {
            $this->modules_container[$name] = null;
            $name = basename($name);

            Shopware()->Hooks()->setAlias($name, $name);
            $proxy = Shopware()->Hooks()->getProxy($name);
            $this->modules_container[$name] = new $proxy;
            $this->modules_container[$name]->sSYSTEM = $this->system;
        }
    }

    /**
     * Reformat module name and return reference to module
     * @param $name
     * @return mixed
     */
    public function getModule($name)
    {
        if (substr($name, 0, 1) == 's') {
            $name = substr($name, 1);
        }
        if (!in_array($name, array('RewriteTable'))) {
            $name = "s" . ucfirst(strtolower($name));
        } else {
            $name = "s" . $name;
        }

        if (!isset($this->modules_container[$name])) {
            $this->loadModule($name);
        }

        return $this->modules_container[$name];
    }

    /**
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return (bool)$this->getModule($offset);
    }

    /**
     * @param $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * @param $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getModule($offset);
    }

    /**
     * @param string $name
     * @param null $value
     * @return mixed
     */
    public function __call($name, $value = null)
    {
        return $this->getModule($name);
    }

    /**
     * @return sArticles
     */
    public function Articles()
    {
        return $this->getModule("Articles");
    }

    /**
     * @return sCategories
     */
    public function Categories()
    {
        return $this->getModule("Categories");
    }

    /**
     * @return sBasket
     */
    public function Basket()
    {
        return $this->getModule("Basket");
    }

    /**
     * @return sMarketing
     */
    public function Marketing()
    {
        return $this->getModule("Marketing");
    }

    /**
     * @return sSystem
     */
    public function System()
    {
        return $this->getModule("System");
    }

    /**
     * @return sConfigurator
     */
    public function Configurator()
    {
        return $this->getModule("Configurator");
    }

    /**
     * @return sAdmin
     */
    public function Admin()
    {
        return $this->getModule("Admin");
    }

    /**
     * @return sOrder
     */
    public function Order()
    {
        return $this->getModule("Order");
    }
}
