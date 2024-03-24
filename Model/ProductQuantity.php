<?php

namespace Biotec\ProductQuantity\Model;

use Magento\Framework\Exception\LocalizedException;

class ProductQuantity
{
    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     */
    public function __construct(
        protected \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        protected \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
    ) {
    }

    /**
     * Get product qty for simple and configurable products
     *
     * @param string $sku
     * @return string
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(string $sku): string
    {
        $product = $this->productRepository->get($sku);

        switch ($product->getTypeId()) {
            case 'simple':
                $stockItem = $this->stockItemRepository->get($product->getEntityId());
                $qty = $stockItem->getQty();
                break;
            case 'configurable':
                $productsCollection = $product->getTypeInstance()->getUsedProducts($product);
                $qty = 0;
                foreach ($productsCollection as $product) {
                    $stockItem = $this->stockItemRepository->get($product->getEntityId());
                    $childQty = $stockItem->getQty();
                    $qty += $childQty;
                }
                break;
            case 'grouped':
            case 'bundle':
            case 'downloadable':
                throw new LocalizedException(
                    __('No individual quantity for grouped, bundle and downloadable products')
                );
        }

        return $qty;
    }
}
