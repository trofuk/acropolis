<?php
namespace Dtrof\Prices\Model\ResourceModel\Sales\Order\Price\Discount;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Dtrof\Prices\Model\Sales\Order\Price\Discount', 'Dtrof\Prices\Model\ResourceModel\Sales\Order\Price\Discount');
    }

}