<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 04.01.17
 * Time: 17:51
 */

namespace Dtrof\Prices\Model\ResourceModel\Sales\Order\Price;


class Discount extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_price_discount', 'order_id');
    }
}