<?php
namespace Dtrof\Prices\Model\ResourceModel\Prices;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dtrof\Prices\Model\Prices', 'Dtrof\Prices\Model\ResourceModel\Prices');
    }

}