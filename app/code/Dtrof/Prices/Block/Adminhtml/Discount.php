<?php
namespace Dtrof\Prices\Block\Adminhtml;

class Discount extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'oyiprices';
        $this->_controller = 'adminhtml_discount';
        $this->buttonList->remove('add');
    }
}