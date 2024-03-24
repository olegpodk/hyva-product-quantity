<?php

declare(strict_types=1);

namespace Biotec\ProductQuantity\Controller\Qty;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;

class Update implements HttpPostActionInterface
{
    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\InventorySales\Model\GetProductSalableQty $getProductSalableQty
     * @param \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        protected \Magento\InventorySales\Model\GetProductSalableQty $getProductSalableQty,
        protected \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        protected \Magento\Framework\App\RequestInterface $request,
        protected \Psr\Log\LoggerInterface $logger
    ) {
    }

    /**
     * Return product quantity
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        try {
            $sku = $this->request->getParam('sku');
            $stockId = $this->getStockIdForCurrentWebsite->execute();
            // Available qty = qty - Reserve Qty - OOS Threshold
            $qty = $this->getProductSalableQty->execute($sku, $stockId);

            $options = [
                'error' => false,
                'product_qty' => $qty
            ];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $options = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        return $this->resultJsonFactory->create()->setData($options);
    }
}
