<?php declare(strict_types=1);

namespace Yireo\Webp2ForHyva\Observer;

use Hyva\GraphqlViewModel\Model\GraphqlQueryEditor;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Throwable;

class AddUrlWebpToGraphqlQuery implements ObserverInterface
{
    private const LINK_TYPES = ['related', 'crosssell', 'upsell'];

    public function __construct(
        private readonly GraphqlQueryEditor $queryEditor
    ) {
    }

    public function execute(Observer $observer): void
    {
        $container = $observer->getData('gql_container');
        if (!$container instanceof DataObject) {
            return;
        }

        $query = $container->getData('query');
        if (!is_string($query) || $query === '') {
            return;
        }

        try {
            $updatedQuery = $this->queryEditor->addFieldIn($query, $this->getPath($observer), 'url_webp');
        } catch (Throwable) {
            return;
        }

        $container->setData('query', $updatedQuery);
    }

    /**
     * @return string[]
     */
    private function getPath(Observer $observer): array
    {
        $type = $observer->getData('type');
        $isLinked = (bool)$observer->getData('is_linked_product')
            && is_string($type)
            && in_array($type, self::LINK_TYPES, true);

        if ($isLinked) {
            return ['products', 'items', $type . '_products', 'small_image'];
        }

        return ['products', 'items', 'small_image'];
    }
}
