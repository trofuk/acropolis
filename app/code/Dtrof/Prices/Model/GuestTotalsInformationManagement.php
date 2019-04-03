<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dtrof\Prices\Model;

class GuestTotalsInformationManagement implements \Magento\Checkout\Api\GuestTotalsInformationManagementInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Checkout\Api\TotalsInformationManagementInterface
     */
    protected $totalsInformationManagement;

    /**
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Api\TotalsInformationManagementInterface $totalsInformationManagement
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Checkout\Api\TotalsInformationManagementInterface $totalsInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->totalsInformationManagement = $totalsInformationManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function calculate(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $calculate = $this->totalsInformationManagement->calculate(
            $quoteIdMask->getQuoteId(),
            $addressInformation
        );
        /**
         * Oyi temporary solution
         */
//        $discount = $this->getCustomDiscount();
//        $subtotal = $calculate->getSubtotal();
//        $base_subtotal = $calculate->getSubtotal();
//        $discount_amount = $subtotal*$discount/100;
//        $base_discount_amount = $base_subtotal*$discount/100;
//        $subtotal_with_discount = $subtotal-$discount_amount;
//        $base_subtotal_with_discount = $base_subtotal-$base_discount_amount;
//        $grand_total = $subtotal-$discount_amount;
//        $base_grand_total = $base_subtotal-$base_discount_amount;
//        $calculate->setDiscountAmount($discount_amount*-1);
//        $calculate->setBaseDiscountAmount($base_discount_amount*-1);
//        $calculate->setSubtotalWithDiscount($subtotal_with_discount);
//        $calculate->setBaseSubtotalWithDiscount($base_subtotal_with_discount);
//        $calculate->setGrandTotal($grand_total);
//        $calculate->setBaseGrandTotal($base_grand_total);
        return $calculate;
    }

    /**
     * @return mixed
     */
    public function getCustomDiscount()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceModel = $objectManager->get('Dtrof\Prices\Model\Prices');
        return $priceModel->getCustomDiscount();
    }
}
