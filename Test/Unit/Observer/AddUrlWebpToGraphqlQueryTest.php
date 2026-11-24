<?php declare(strict_types=1);

namespace Yireo\Webp2ForHyva\Test\Unit\Observer;

use Hyva\GraphqlViewModel\Model\GraphqlQueryEditor;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;
use Yireo\Webp2ForHyva\Observer\AddUrlWebpToGraphqlQuery;

class AddUrlWebpToGraphqlQueryTest extends TestCase
{
    private const PLAIN_QUERY = '{ products(filter: {}) { items { sku small_image { url } } } }';
    private const LINKED_QUERY = '{ products(filter: {}) { items { related_products { sku small_image { url } } } } }';

    public function testAddsUrlWebpToNonLinkedProducts(): void
    {
        $query = $this->runObserver(self::PLAIN_QUERY, ['type' => '', 'is_linked_product' => false]);

        $this->assertMatchesRegularExpression('/small_image\s*\{\s*url\s+url_webp\s*\}/', $query);
        $this->assertStringNotContainsString('_products', $query);
    }

    public function testAddsUrlWebpToLinkedProducts(): void
    {
        $query = $this->runObserver(self::LINKED_QUERY, ['type' => 'related', 'is_linked_product' => true]);

        $this->assertMatchesRegularExpression(
            '/related_products\s*\{\s*sku\s+small_image\s*\{\s*url\s+url_webp\s*\}/',
            $query
        );
    }

    public function testUnknownTypeFallsBackToNonLinkedPath(): void
    {
        $query = $this->runObserver(self::PLAIN_QUERY, ['type' => 'new', 'is_linked_product' => true]);

        $this->assertStringNotContainsString('new_products', $query);
        $this->assertMatchesRegularExpression('/small_image\s*\{\s*url\s+url_webp\s*\}/', $query);
    }

    public function testKnownTypeWithoutLinkedFlagFallsBackToNonLinkedPath(): void
    {
        $query = $this->runObserver(self::PLAIN_QUERY, ['type' => 'related', 'is_linked_product' => false]);

        $this->assertStringNotContainsString('related_products', $query);
        $this->assertMatchesRegularExpression('/small_image\s*\{\s*url\s+url_webp\s*\}/', $query);
    }

    public function testInvalidQueryIsLeftUntouched(): void
    {
        $invalidQuery = '{ products( ';

        $this->assertSame($invalidQuery, $this->runObserver($invalidQuery, []));
    }

    public function testMissingContainerDoesNotThrow(): void
    {
        $observer = new Observer([]);
        (new AddUrlWebpToGraphqlQuery(new GraphqlQueryEditor()))->execute($observer);

        $this->assertNull($observer->getData('gql_container'));
    }

    /**
     * @param array<string, mixed> $eventData
     */
    private function runObserver(string $query, array $eventData): string
    {
        $container = new DataObject(['query' => $query]);
        $observer = new Observer(array_merge($eventData, ['gql_container' => $container]));

        (new AddUrlWebpToGraphqlQuery(new GraphqlQueryEditor()))->execute($observer);

        return (string)$container->getData('query');
    }
}
