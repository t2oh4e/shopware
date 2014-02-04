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

use Shopware\Models\Analytics\Repository;

/**
 * @category  Shopware
 * @package   Shopware\Tests
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Tests_Controllers_Backend_AnalyticsTest extends Enlight_Components_Test_Controller_TestCase
{
    /**@var Shopware\Models\Analytics\Repository*/
    private $repository;

    private $userId;
    private $customerNumber;
    private $articleId;
    private $categoryId;
    private $orderNumber;
    private $articleDetailId;
    private $orderIds;

    /**
     * Standard set up for every test - just disable auth
     */
    public function setUp()
    {
        parent::setUp();

        // disable auth and acl
        Shopware()->Plugins()->Backend()->Auth()->setNoAuth();
        Shopware()->Plugins()->Backend()->Auth()->setNoAcl();

        $this->repository = new Repository(
            Shopware()->Models()->getConnection(),
            Shopware()->Events()
        );

        $this->setUpDemoData();
    }

    public function tearDown()
    {
        $this->removeDemoData();
    }

    private function setUpDemoData()
    {
        $this->customerNumber = uniqid();
        $this->orderNumber = uniqid('SW');

        Shopware()->Db()->insert('s_user', array(
            'password' => '098f6bcd4621d373cade4e832627b4f6', // md5('test')
            'encoder' => 'md5',
            'email' => uniqid('test') . '@test.com',
            'active' => '1',
            'firstlogin' => '2013-06-01',
            'lastlogin' => '2013-07-01',
            'subshopID' => '1',
            'customergroup' => 'EK'
        ));
        $this->userId = Shopware()->Db()->lastInsertId();

        Shopware()->Db()->insert('s_articles', array(
            'supplierID' => 1,
            'name' => 'PHPUNIT ARTICLE',
            'datum' => '2013-06-01',
            'active' => 1,
            'taxID' => 1,
            'main_detail_id' => 0
        ));
        $this->articleId = Shopware()->Db()->lastInsertId();

        Shopware()->Db()->insert('s_articles_details', array(
            'articleID' => $this->articleId,
            'ordernumber' => $this->orderNumber,
            'kind' => 1,
            'active' => 1,
            'instock' => 1
        ));
        $this->articleDetailId = Shopware()->Db()->lastInsertId();

        Shopware()->Db()->update(
            's_articles',
            array('main_detail_id' => $this->articleDetailId),
            'id = ' . $this->articleId
        );

        Shopware()->Db()->insert('s_categories', array(
            'description' => 'phpunit category',
            'active' => 1
        ));
        $this->categoryId = Shopware()->Db()->lastInsertId();

        Shopware()->Db()->insert('s_articles_categories_ro', array(
            'articleID' => $this->articleId,
            'categoryID' => $this->categoryId
        ));

        $orders = array(
            array(
                'userID' => $this->userId,
                'invoice_amount' => '1000',
                'invoice_amount_net' => '840',
                'ordertime' => '2013-06-01 10:11:12',
                'status' => 0,
                'partnerID' => 'PHPUNIT_PARTNER',
                'referer' => 'http://www.google.de/',
                'subshopID' => 1,
                'currencyFactor' => 1,
                'dispatchID' => 9,
                'paymentID' => 2
            ),
            array(
                'userID' => $this->userId,
                'invoice_amount' => '500',
                'invoice_amount_net' => '420',
                'ordertime' => '2013-06-15 10:11:12',
                'status' => '-1',
                'subshopID' => 1,
                'currencyFactor' => 1,
                'dispatchID' => 9,
                'paymentID' => 2
            )
        );
        $this->orderIds = array();
        foreach($orders as $order) {
            Shopware()->Db()->insert('s_order', $order);
            array_push($this->orderIds, Shopware()->Db()->lastInsertId());
        }

        $orderDetails = array(
            array(
                'orderID' => $this->orderIds[0],
                'articleID' => $this->articleId,
                'articleordernumber' => $this->orderNumber,
                'price' => 1000,
                'quantity' => 1,
                'modus' => 0,
                'taxID' => 1,
                'tax_rate' => 19
            ),
            array(
                'orderID' => $this->orderIds[1],
                'articleID' => $this->articleId,
                'articleordernumber' => $this->orderNumber,
                'price' => 1000,
                'quantity' => 1,
                'modus' => 0,
                'taxID' => 1,
                'tax_rate' => 19
            )
        );
        foreach($orderDetails as $detail) {
            Shopware()->Db()->insert('s_order_details', $detail);
        }

        $userBillingAddress = array(
            'userID' => $this->userId,
            'company' => 'PHPUNIT',
            'salutation' => 'mr',
            'customernumber' => $this->customerNumber,
            'countryID' => 2,
            'stateID' => 3,
            'birthday' => '1990-01-01'
        );
        Shopware()->Db()->insert('s_user_billingaddress', $userBillingAddress);

        $orderBillingAddresses = array(
            array(
                'userID' => $this->userId,
                'orderID' => $this->orderIds[0],
                'company' => $userBillingAddress['company'],
                'salutation' => $userBillingAddress['salutation'],
                'customernumber' => $this->customerNumber,
                'countryID' => $userBillingAddress['countryID'],
                'stateID' => $userBillingAddress['stateID'],
            ),
            array(
                'userID' => $this->userId,
                'orderID' => $this->orderIds[1],
                'company' => $userBillingAddress['company'],
                'salutation' => $userBillingAddress['salutation'],
                'customernumber' => $this->customerNumber,
                'countryID' => $userBillingAddress['countryID'],
                'stateID' => $userBillingAddress['stateID'],
            )
        );
        foreach($orderBillingAddresses as $address) {
            Shopware()->Db()->insert('s_order_billingaddress', $address);
        }

        $visitors = array(
            array(
                'shopID' => 1,
                'datum' => '2013-06-15',
                'pageimpressions' => 500,
                'uniquevisits' => 20
            ),
            array(
                'shopID' => 1,
                'datum' => '2013-06-01',
                'pageimpressions' => 300,
                'uniquevisits' => 10
            )
        );
        foreach($visitors as $visitor) {
            Shopware()->Db()->insert('s_statistics_visitors', $visitor);
        }

        Shopware()->Db()->insert('s_statistics_article_impression', array(
            'articleId' => $this->articleId,
            'shopId' => 1,
            'date' => '2013-06-15',
            'impressions' => 10
        ));

        Shopware()->Db()->insert('s_statistics_search', array(
            'datum' => '2013-06-15 10:11:12',
            'searchterm' => 'phpunit search term',
            'results' => 10
        ));

        Shopware()->Db()->insert('s_statistics_referer', array(
            'datum' => '2013-06-15',
            'referer' => 'http://www.google.de/?q=phpunit'
        ));
    }

    private function removeDemoData()
    {
        Shopware()->Db()->delete('s_user', 'id = ' . $this->userId);
        Shopware()->Db()->delete('s_user_billingaddress', 'userID = ' . $this->userId);
        Shopware()->Db()->delete('s_order', 'userID = ' . $this->userId);
        Shopware()->Db()->delete('s_articles', 'id = ' . $this->articleId);
        Shopware()->Db()->delete('s_articles_details', 'articleID = ' . $this->articleId);
        Shopware()->Db()->delete('s_statistics_visitors', 'shopID = 1');
        Shopware()->Db()->delete('s_statistics_article_impression', 'articleId = ' . $this->articleId);
        Shopware()->Db()->delete('s_order_billingaddress', 'userID = ' . $this->userId);
        Shopware()->Db()->delete('s_order_details', 'articleID = ' . $this->articleId);
        Shopware()->Db()->delete('s_statistics_search', "searchterm = 'phpunit search term'");
        Shopware()->Db()->delete('s_statistics_referer', "referer = 'http://www.google.de/?q=phpunit'");
        Shopware()->Db()->delete('s_categories', 'id = ' . $this->categoryId);
        Shopware()->Db()->delete(
            's_articles_categories_ro',
            array(
                'articleID' => $this->articleId,
                'categoryID' => $this->categoryId
            )
        );
    }

    public function testGetVisitorImpressions()
    {
        $result = $this->repository->getVisitorImpressions(
            0,
            25,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'datum' => '2013-06-01',
                    'totalImpressions' => 300,
                    'totalVisits' => 10
                ),
                array(
                    'datum' => '2013-06-15',
                    'totalImpressions' => 500,
                    'totalVisits' => 20
                )
            )
        );
    }

    public function testGetOrdersOfCustomers()
    {
        $result = $this->repository->getOrdersOfCustomers(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderTime' => '2013-06-01',
                    'isNewCustomerOrder' => 1,
                    'salutation' => 'mr',
                    'userId' => $this->userId
                )
            )
        );
    }

    public function testGetReferrerRevenue()
    {
        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->getActiveDefault();
        $shop->registerResources(Shopware()->Bootstrap());

        $result = $this->repository->getReferrerRevenue(
            $shop,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'turnover' => 1000.00,
                    'userID' => $this->userId,
                    'referrer' => 'http://www.google.de/',
                    'firstLogin' => '2013-06-01',
                    'orderTime' => '2013-06-01'
                )
            )
        );
    }

    public function testGetPartnerRevenue()
    {
        $result = $this->repository->getPartnerRevenue(
            0,
            25,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'turnover' => 1000,
                    'partner' => null,
                    'trackingCode' => 'PHPUNIT_PARTNER',
                    'partnerId' => null
                )
            )
        );
    }

    public function testGetProductSales()
    {
        $result = $this->repository->getProductSales(
            0,
            25,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'sales' => 1,
                    'name' => 'PHPUNIT ARTICLE',
                    'ordernumber' => $this->orderNumber
                )
            )
        );
    }

    public function testGetProductImpressions()
    {
        $result = $this->repository->getProductImpressions(
            0,
            25,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'articleId' => $this->articleId,
                    'articleName' => 'PHPUNIT ARTICLE',
                    'date' => 1371247200,
                    'totalImpressions' => 10
                )
            )
        );
    }

    public function testGetAgeOfCustomers()
    {
        $result = $this->repository->getAgeOfCustomers(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'firstLogin' => '2013-06-01',
                    'birthday' => '1990-01-01'
                )
            )
        );
    }

    public function testGetAmountPerHour()
    {
        $result = $this->repository->getAmountPerHour(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'date' => '1970-01-01 10:00:00'
                )
            )
        );
    }

    public function testGetAmountPerWeekday()
    {
        $result = $this->repository->getAmountPerWeekday(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'date' => '2013-06-01'
                )
            )
        );
    }

    public function testGetAmountPerCalendarWeek()
    {
        $result = $this->repository->getAmountPerCalendarWeek(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'date' => '2013-05-30'
                )
            )
        );
    }

    public function testGetAmountPerMonth()
    {
        $result = $this->repository->getAmountPerMonth(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'date' => '2013-06-04'
                )
            )
        );
    }

    public function testGetCustomerGroupAmount()
    {
        $result = $this->repository->getCustomerGroupAmount(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'customerGroup' => 'Shopkunden'
                )
            )
        );
    }

    public function testGetAmountPerCountry()
    {
        $result = $this->repository->getAmountPerCountry(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'name' => 'Deutschland'
                )
            )
        );
    }

    public function testGetAmountPerShipping()
    {
        $result = $this->repository->getAmountPerShipping(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'name' => 'Standard Versand'
                )
            )
        );
    }

    public function testGetAmountPerPayment()
    {
        $result = $this->repository->getAmountPerPayment(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'displayDate' => 'Saturday',
                    'name' => 'Lastschrift'
                )
            )
        );
    }

    public function testGetSearchTerms()
    {
        $result = $this->repository->getSearchTerms(
            0,
            25,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'countRequests' => 1,
                    'searchterm' => 'phpunit search term',
                    'countResults' => 10
                )
            )
        );
    }

    public function testGetDailyVisitors()
    {
        $result = $this->repository->getDailyVisitors(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                '2013-06-15' => array(
                    array(
                        'clicks' => 500,
                        'visits' => 20
                    )
                ),
                '2013-06-01' => array(
                    array(
                        'clicks' => 300,
                        'visits' => 10
                    )
                )
            )
        );
    }

    public function testGetDailyShopVisitors()
    {
        $result = $this->repository->getDailyShopVisitors(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                '2013-06-15' => array(
                    array(
                        'clicks' => 500,
                        'visits' => 20
                    )
                ),
                '2013-06-01' => array(
                    array(
                        'clicks' => 300,
                        'visits' => 10
                    )
                )
            )
        );
    }

    public function testGetDailyShopOrders()
    {
        $result = $this->repository->getDailyShopOrders(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                '2013-06-15' => array(
                    array(
                        'orderCount' => 0,
                        'cancelledOrders' => 1
                    )
                ),
                '2013-06-01' => array(
                    array(
                        'orderCount' => 1,
                        'cancelledOrders' => 0
                    )
                )
            )
        );
    }

    public function testGetDailyRegistrations()
    {
        $result = $this->repository->getDailyRegistrations(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                '2013-06-01' => array(
                    array(
                        'registrations' => 1
                    )
                )
            )
        );
    }

    public function testGetDailyTurnover()
    {
        $result = $this->repository->getDailyTurnover(
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                '2013-06-01' => array(
                    array(
                        'orderCount' => 1,
                        'turnover' => 1000
                    )
                )
            )
        );
    }

    public function testGetProductAmountPerManufacturer()
    {
        $result = $this->repository->getProductAmountPerManufacturer(
            0,
            25,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'name' => 'shopware AG'
                )
            )
        );
    }

    public function testGetVisitedReferrer()
    {
        $result = $this->repository->getVisitedReferrer(
            0,
            25,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'count' => 1,
                    'referrer' => 'http://www.google.de/?q=phpunit'
                )
            )
        );
    }

    public function testGetReferrerUrls()
    {
        $result = $this->repository->getReferrerUrls(
            'google.de',
            0,
            25
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'count' => 1,
                    'referrer' => 'http://www.google.de/?q=phpunit'
                )
            )
        );
    }

    public function testGetReferrerSearchTerms()
    {
        $result = $this->repository->getReferrerSearchTerms('google.de');
        $data = $result->getData();

        $this->assertEquals(
            $data,
            array(
                array(
                    'count' => 1,
                    'referrer' => 'http://www.google.de/?q=phpunit'
                )
            )
        );

        $this->assertEquals(
            $this->getSearchTermFromReferrerUrl($data[0]['referrer']),
            'phpunit'
        );
    }

    private function getSearchTermFromReferrerUrl($url)
    {
        preg_match_all("#[?&]([qp]|query|highlight|encquery|url|field-keywords|as_q|sucheall|satitle|KW)=([^&\\$]+)#", utf8_encode($url) . "&", $matches);
        if(empty($matches[0])){
            return '';
        }

        $ref = $matches[2][0];
        $ref = html_entity_decode(rawurldecode(strtolower($ref)));
        $ref = str_replace('+', ' ', $ref);
        $ref = trim(preg_replace('/\s\s+/', ' ', $ref));

        return $ref;
    }

    public function testGetProductAmountPerCategory()
    {
        $result = $this->repository->getProductAmountPerCategory(
            0,
            new DateTime('2013-01-01'),
            new DateTime('2014-01-01')
        );

        $this->assertEquals(
            $result->getData(),
            array(
                array(
                    'orderCount' => 1,
                    'turnover' => 1000,
                    'name' => 'phpunit category',
                    'node' => ''
                )
            )
        );
    }
}
