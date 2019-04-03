<?php
namespace Dtrof\Prices\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Prices  extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Discount Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceCollection;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
//        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = [])
    {
        $this->_resourceCollection = $resourceCollection;
//        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dtrof\Prices\Model\ResourceModel\Prices');
    }

    public function checkCustomerGroup()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        return $customerSession->getCustomerGroupId();
    }

    public function isActive()
    {
        return ($this->getActive() == self::STATUS_ENABLED);
    }

    public function getDiscountForCustomer($store_id)
    {
        $find = [];
        $collection  = $this->getCollection();
        $collection->addFieldToFilter('customer_group_id',$this->checkCustomerGroup())
            ->addFieldToFilter('store_id',['in'=>['0', $store_id]]);
        if(count($collection) > 0){
            foreach($collection as $discount){
                if($discount->getActive() == self::STATUS_ENABLED ){
                    $find = $discount;
                    if($discount->getStoreId() == $store_id){
                        continue;
                    }
                }
            }
        }
        return $find;
    }

    public function getActiveDiscount($store_id)
    {
        $disc = $this->getActiveDiscountCollection($store_id);
        $a = [];
        if(count($disc) > 0){
            foreach ($disc as $d){
                $a[$d->getCustomerGroupId()] = $d;
            }
        }

        return $a;
    }

    public function getActiveDiscountCollection($store_id)
    {
        $collection  = $this->getCollection();
        $collection->addFieldToFilter('store_id',['in'=>['0', $store_id]])
            ->addFieldToFilter('active', self::STATUS_ENABLED)
            ->setOrder('id','ASC')
            ->setOrder('sort_order','ASC');

        return $collection;
    }

    public function calculateDiscount($price,$collection)
    {
        $calc = [];
        $customer_group_id = $this->checkCustomerGroup();
        $cart_total = $this->getCartSubtotal();
        if(count($collection) > 0){
            $i = 0;
            foreach($collection as $disc){
                if($disc->getDiscount() > 0){
                    $calculate_price = $price - (($price * $disc->getDiscount())/100);
                    $calc[$i] = [
                        'discount_price_text' => $this->getFormatCurrency($calculate_price),
                        'discount_price' => $calculate_price,
                        'label' =>$disc->getName(),
                        'store_id' => $disc->getStoreId(),
                        'message' => $disc->getMessage(),
                        'price' => $price,
                        'discount'=>$disc->getDiscount(),
                        'min_price' => $disc->getPriceFrom(),
                        'max_price' => $disc->getPriceTo(),
                        'active' => 0,
                        'label_in' => ($customer_group_id != $disc->getCustomerGroupId())? true:false
                    ];

                    if($i > 0){
                        $calc[$i]['quantity'] = $this->calculateDiscountQuantity($calc[$i-1]['discount_price'],
                            $calc[$i]['min_price'],
                            $cart_total);
                    }else{
                        $calc[$i]['quantity'] = $this->calculateDiscountQuantity($price,$disc->getPriceFrom(),$cart_total);
                    }
                    if($cart_total > 0 && $cart_total >= $disc->getPriceFrom() && $cart_total <= $disc->getPriceTo()){
                        $calc[$i]['active'] = 1;
                    }
                    $i++;
                }
            }
        }

        return $calc;
    }

    public function getFormatCurrency($price,
                                      $includeContainer = true,
                                      $precision = PriceCurrencyInterface::DEFAULT_PRECISION)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceCurrency = $objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface');
        return $priceCurrency->format($price, $includeContainer, $precision);
    }

    public function getCartSubtotal()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('Magento\Checkout\Model\Cart');
        return $cart->getQuote()->getSubtotal();
    }

    public function calculateDiscountQuantity($price, $min_price,$cart)
    {
        if($cart > 0){
            if($cart > $min_price){
                $quantity = $cart/$price;
            }else{
                $quantity = ($min_price - $cart)/$price;
            }
        }else{
            $quantity = $min_price/$price;
        }

        return ceil($quantity);
    }

    public function getDiscountForCart()
    {
        $subtotal = (int)$this->getCartSubtotal();

        if($subtotal > 0){
            $collection  = $this->getCollection();
            $collection->addFieldToFilter('active', self::STATUS_ENABLED)
                ->addFieldToFilter('price_from',['to' => $subtotal])
                ->addFieldToFilter('price_to',['from' => $subtotal]);
            if(count($collection) > 0) {
                return $collection;
            } else {
                $collection  = $this->getCollection();
                $collection->addFieldToFilter('active', self::STATUS_ENABLED)
                    ->addFieldToFilter('price_to',['to' => $subtotal]);
                if(count($collection) > 0) {
                    return $collection;
                }
            }
        }

        return [];
    }

    public function getCartDiscount()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $discount = 0; $arr = [];
        $store_id = $storeManager->getStore()->getId();
        $collection = $this->getDiscountForCart();

        if(!empty($collection) && count($collection) > 0){
            foreach ($collection as $k=>$v){
                $arr[$v->getStoreId()] = $v->getDiscount();
            }
            $discount = (isset($arr[$store_id]))? $arr[$store_id]:$arr[0];
        }
        return $discount;
    }

    public function getCustomDiscount()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store_id = $storeManager->getStore()->getId();
        $discount = 0;
        $user_discount = $this->getDiscountForCustomer($store_id);
        if(count($user_discount) > 0){
            $discount = $user_discount->getDiscount();
        }else{
            $arr = [];
            $collection = $this->getDiscountForCart();
            if(!empty($collection) && count($collection) > 0){
                foreach ($collection as $k=>$v){
                    $arr[$v->getStoreId()] = $v->getDiscount();
                }

                $discount = (isset($arr[$store_id]))? $arr[$store_id]:$arr[0];
            }
        }

        return $discount;
    }

}