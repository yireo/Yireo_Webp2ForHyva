<?php declare(strict_types=1);

namespace Yireo\Webp2ForHyva\Observer;

use Hyva\GraphqlViewModel\Model\GraphqlQueryEditor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddUrlWebpToGraphqlQuery implements ObserverInterface
{
    /**
     * @var GraphqlQueryEditor
     */
    private $queryEditor;

    public function __construct(GraphqlQueryEditor $queryEditor)
    {
        $this->queryEditor = $queryEditor;
    }

    // @todo: Create an integration test for this
    public function execute(Observer $observer)
    {
        $query = $observer->getData('gql_container')->getData('query');
        $linkType  = $observer->getData('type');
        $path  = ['products', 'items', ($linkType ? "{$linkType}_products" : 'products'), 'small_image'];

        $updatedQuery = $this->queryEditor->addFieldIn($query, $path, 'url_webp');
        
        $observer->getData('gql_container')->setData('query', $updatedQuery);
    }
}
