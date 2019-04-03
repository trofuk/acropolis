<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dtrof\Prices\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class GuestPaymentInformationManagement implements \Magento\Checkout\Api\GuestPaymentInformationManagementInterface
{

    /**
     * @var \Magento\Quote\Api\GuestBillingAddressManagementInterface
     */
    protected $billingAddressManagement;

    /**
     * @var \Magento\Quote\Api\GuestPaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Quote\Api\GuestCartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    protected $paymentInformationManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @param \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement
     * @param \Magento\Quote\Api\GuestPaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Api\GuestCartManagementInterface $cartManagement
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement,
        \Magento\Quote\Api\GuestPaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\GuestCartManagementInterface $cartManagement,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository
    ) {
        $this->billingAddressManagement = $billingAddressManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartManagement = $cartManagement;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function savePaymentInformationAndPlaceOrder(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $this->savePaymentInformation($cartId, $email, $paymentMethod, $billingAddress);
        try {
            $orderId = $this->cartManagement->placeOrder($cartId);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('An error occurred on the server. Please try to place the order again.'),
                $e
            );
        }
        return $orderId;
    }

    /**
     * {@inheritDoc}
     */
    public function savePaymentInformation(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($billingAddress) {
            $billingAddress->setEmail($email);
            $this->billingAddressManagement->assign($cartId, $billingAddress);
        } else {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $this->cartRepository->getActive($quoteIdMask->getQuoteId())->getBillingAddress()->setEmail($email);
        }

        $this->paymentMethodManagement->set($cartId, $paymentMethod);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentInformation($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $getPaymentInformation = $this->paymentInformationManagement->getPaymentInformation($quoteIdMask->getQuoteId());
//        $getTotals = $getPaymentInformation->getTotals();
//        $discount = $this->getCustomDiscount();
//        foreach($getTotals->getItems() as $index => $item) {
//            if($discount > 0){
//                $qty = $item->getQty();
//                $price = $item->getPrice() -(($item->getPrice()*$discount)/100);
//                $base_price = $item->getBasePrice() -(($item->getBasePrice()*$discount)/100);
//                $price_inc_tax = $item->getPriceInclTax() -(($item->getPriceInclTax()*$discount)/100);
//                $base_price_inc_tax = $item->getBasePriceInclTax() - (($item->getBasePriceInclTax()*$discount)/100);
//                $item->setPrice($price);
//                $item->setBasePrice($base_price);
//                $item->setPriceInclTax($price_inc_tax);
//                $item->setRowTotal($price * $qty);
//                $item->setBaseRowTotal($base_price * $qty);
//                $item->setRowTotalInclTax($price_inc_tax * $qty);
//                $item->setBaseRowTotalInclTax($base_price_inc_tax * $qty);
//            }
//        }
        return $getPaymentInformation;
    }

    public function getCustomDiscount()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceModel = $objectManager->get('Dtrof\Prices\Model\Prices');
        return $priceModel->getCustomDiscount();
    }
}
