<?php
namespace Dtrof\Prices\Block\Adminhtml\Discount\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('discount_form');
        $this->setTitle(__('Edit Discount'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('oyi_price_groups');
        /* @var $model \Magento\Cms\Model\Page */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $page_model = $objectManager->create('\Magento\Cms\Model\Page');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' =>
                [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype'=>'multipart/form-data'
                ]
            ]
        );
        $customer_group_id = $model->getData('customer_group_id');
        $group_model = $objectManager->get('\Magento\Customer\Model\Group')->load($customer_group_id);
        $fieldset = $form->addFieldset(
            'edit_form',
            ['legend' => __('Discount Information for customer group - ').' '.$group_model->getCode(),
            'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'price_from',
            'text',
            [
                'name' => 'price[price_from]',
                'label' => __('Min Price'),
                'title' => __('Min Price'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'price_to',
            'text',
            [
                'name' => 'price[price_to]',
                'label' => __('Max Price'),
                'title' => __('Max Price'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'discount',
            'text',
            [
                'name' => 'price[discount]',
                'label' => __('Discount'),
                'title' => __('Discount'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'price[name]',
                'label' => __('Discount Label'),
                'title' => __('Discount Label'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'message',
            'text',
            [
                'name' => 'price[message]',
                'label' => __('Short message'),
                'title' => __('Short message'),
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'label' => __('Sort'),
                'title' => __('Sort'),
                'name' => 'price[sort_order]'
            ]
        );

        $fieldset->addField(
            'active',
            'select',
            [
                'label' => __('Discount Status'),
                'title' => __('Discount Status'),
                'name' => 'price[active]',
                'required' => true,
                'options' => $page_model->getAvailableStatuses(),
            ]
        );

        $fieldset->addField('id', 'hidden', ['name' => 'id']);

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name' => 'post[store_id]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                'disabled' => 'disabled',
            ]
        );

        $form->setValues($model->getData());

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
