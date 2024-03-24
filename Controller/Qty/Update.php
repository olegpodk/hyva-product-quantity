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
     * @param \Biotec\ProductQuantity\Model\ProductQuantity $productQuantity
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        protected \Biotec\ProductQuantity\Model\ProductQuantity $productQuantity,
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
            $qty = $this->productQuantity->execute($sku);

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
