<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 04.01.17
 * Time: 17:51
 */

namespace Dtrof\Prices\Model\Sales\Order\Price;


class Discount extends \Magento\Framework\Model\AbstractModel
{
    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Dtrof\Prices\Model\ResourceModel\Sales\Order\Price\Discount $resource,
        \Dtrof\Prices\Model\ResourceModel\Sales\Order\Price\Discount\Collection $resourceCollection)
    {
        parent::__construct($context, $registry, $resource, $resourceCollection);

    }
}