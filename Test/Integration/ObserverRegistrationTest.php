<?php declare(strict_types=1);

namespace Yireo\Webp2ForHyva\Test\Integration;

use Magento\Framework\Event\ConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\Webp2ForHyva\Observer\AddUrlWebpToGraphqlQuery;

/**
 * @magentoAppArea frontend
 */
class ObserverRegistrationTest extends TestCase
{
    public function testObserverIsRegisteredForSliderQueryEvent(): void
    {
        $eventConfig = Bootstrap::getObjectManager()->get(ConfigInterface::class);
        $this->assertInstanceOf(ConfigInterface::class, $eventConfig);

        $observers = $eventConfig->getObservers('hyva_graphql_render_before_slider_products_query');

        $this->assertArrayHasKey('Yireo_Webp2ForHyva::addUrlWebpToGraphqlQuery', $observers);
        $this->assertSame(
            AddUrlWebpToGraphqlQuery::class,
            ltrim($observers['Yireo_Webp2ForHyva::addUrlWebpToGraphqlQuery']['instance'], '\\')
        );
    }
}
