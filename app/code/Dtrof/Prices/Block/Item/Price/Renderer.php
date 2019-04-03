<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dtrof\Prices\Block\Item\Price;

/**
 * Item price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Checkout\Block\Item\Price\Renderer
{
   public function getCustomDiscount()
   {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $priceModel = $objectManager->get('Dtrof\Prices\Model\Prices');
       return $priceModel->getCustomDiscount();
   }
}
