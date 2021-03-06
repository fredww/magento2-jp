<?php
namespace MagentoJapan\Price\Test\Unit\Model\Directory\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class FormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Price Format Plugin
     *
     * @var \MagentoJapan\Price\Model\Directory\Plugin\Format
     */
    protected $formatPlugin;

    /**
     * Price Currency
     *
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $priceCurrency;

    /**
     * Price container closure
     *
     * @var \Closure
     */
    protected $closure;

    /**
     * Setup
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->formatPlugin = $objectManager->getObject(
            'MagentoJapan\Price\Model\Directory\Plugin\Format'
        );

        $this->priceCurrency = $this->getMockBuilder(
            'Magento\Directory\Model\PriceCurrency'
        )->disableOriginalConstructor()->getMock();

        $this->closure = function () {
            return '<span class="price">￥100.49</span>';
        };
    }

    /**
     * Test for aroundFormat to JPY
     *
     * @return void
     */
    public function testJpyAroundFormat()
    {
        $currency = $this->getMock(
            'Magento\Directory\Model\Currency',
            [],
            [],
            '',
            false
        );
        $currency->expects($this->atLeastOnce())
            ->method('getCode')->willReturn('JPY');
        $currency->expects($this->atLeastOnce())
            ->method('formatPrecision')
            ->willReturn('<span class="price">￥100</span>');

        $this->priceCurrency->expects($this->atLeastOnce())
            ->method('getCurrency')->willReturn($currency);

        $this->assertEquals(
            '<span class="price">￥100</span>',
            $this->formatPlugin->aroundFormat(
                $this->priceCurrency,
                $this->closure,
                100.49,
                2,
                null,
                null
            )
        );
    }

    /**
     * Test for aroundFormat to others
     *
     * @return void
     */
    public function testNonJpyAroundFormat()
    {
        $currency = $this->getMockBuilder('Magento\Directory\Model\Currency')
            ->disableOriginalConstructor()
            ->getMock();
        $currency->expects($this->atLeastOnce())
            ->method('getCode')->willReturn('USD');

        $this->priceCurrency->expects($this->atLeastOnce())
            ->method('getCurrency')->willReturn($currency);

        $this->assertNotEquals(
            '<span class="price">￥100</span>',
            $this->formatPlugin->aroundFormat(
                $this->priceCurrency,
                $this->closure,
                100.49,
                2,
                null,
                null
            )
        );
    }
}