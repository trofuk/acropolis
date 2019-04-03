<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dtrof\Prices\Model;

use Magento\Framework\Exception\CouldNotSaveException;

class PaymentInformationManagement implements \Magento\Checkout\Api\PaymentInformationManagementInterface
{
    /**
     * @var \Magento\Quote\Api\BillingAddressManagementInterface
     */
    protected $billingAddressManagement;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var PaymentDetailsFactory
     */
    protected $paymentDetailsFactory;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalsRepository;

    /**
     * @param \Magento\Quote\Api\BillingAddressManagementInterface $billingAddressManagement
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Api\BillingAddressManagementInterface $billingAddressManagement,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
    ) {
        $this->billingAddressManagement = $billingAddressManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartManagement = $cartManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function savePaymentInformationAndPlaceOrder(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $this->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
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
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($billingAddress) {
            $this->billingAddressManagement->assign($cartId, $billingAddress);
        }
        $this->paymentMethodManagement->set($cartId, $paymentMethod);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentInformation($cartId)
    {
        /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $getTotals = $this->cartTotalsRepository->get($cartId);
        $discount = $this->getCustomDiscount();
        foreach($getTotals->getItems() as $index => $item) {
            if($discount > 0){
                $qty = $item->getQty();
                $price = $item->getPrice() -(($item->getPrice()*$discount)/100);
                $base_price = $item->getBasePrice() -(($item->getBasePrice()*$discount)/100);
                $price_inc_tax = $item->getPriceInclTax() -(($item->getPriceInclTax()*$discount)/100);
                $base_price_inc_tax = $item->getBasePriceInclTax() - (($item->getBasePriceInclTax()*$discount)/100);
                $item->setPrice($price);
                $item->setBasePrice($base_price);
                $item->setPriceInclTax($price_inc_tax);
                $item->setRowTotal($price * $qty);
                $item->setBaseRowTotal($base_price * $qty);
                $item->setRowTotalInclTax($price_inc_tax * $qty);
                $item->setBaseRowTotalInclTax($base_price_inc_tax * $qty);
            }
        }
        $paymentDetails->setTotals($getTotals);
        return $paymentDetails;
    }
    
    public function getCustomDiscount()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceModel = $objectManager->get('Dtrof\Prices\Model\Prices');
        return $priceModel->getCustomDiscount();
    }
}
