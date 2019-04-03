<?php
namespace Dtrof\Prices\Model\Total\Quote;
/**
 * Class Custom
 * @package MageHit\Webpos\Model\Total\Quote
 */
class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * Custom constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ){
        $this->_priceCurrency = $priceCurrency;
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|bool
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $discount = $this->getCartDiscount($quote->getStoreId());
        if($discount > 0){
            $discountAmount      = - ($total->getSubtotal()*$discount)/100;
            $baseDiscountAmount      = - ($total->getBaseSubtotal() * $discount)/100;

            $total->addTotalAmount('customdiscount', $discountAmount);
            $total->addBaseTotalAmount('customdiscount', $baseDiscountAmount);
            $quote->setCustomDiscount($discountAmount);


        }

        return $this;
    }

    public function getCartDiscount($store_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceModel = $objectManager->get('Dtrof\Prices\Model\Prices');
        $user_discount = $priceModel->getDiscountForCustomer($store_id);
        if(count($user_discount) > 0){
            $discount = $user_discount->getDiscount();
        }else{
            $discount = $priceModel->getCartDiscount();
        }

        return (int)$discount;
    }

    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $discount = $this->getCartDiscount($quote->getStoreId());
        if($discount > 0){
            $discountAmount      = - ($total->getSubtotal()*$discount)/100;
            return [
                'code' => 'customdiscount',
                'title' => $this->getLabel(),
                'value' => $discountAmount
            ];
        }

    }

    /**
     * get label
     * @return string
     */
    public function getLabel()
    {
        return __('Customer Discount');
    }
}