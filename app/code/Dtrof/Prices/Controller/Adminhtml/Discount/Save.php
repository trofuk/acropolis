<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dtrof\Prices\Controller\Adminhtml\Discount;

use Magento\Backend\App\Action;

class Save extends Action
{


    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dtrof_Gifts::save');
    }


    /**
     * Save product action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $post = $this->getRequest()->getPostValue();
            $data = $post['price'];
            $id = (int)$this->getRequest()->getParam('id');
            $model = $this->_objectManager->create('Dtrof\Prices\Model\Prices');
            
            try {

                if($id > 0){
                    $model->load($id);

                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }

                    $model->addData($data);
                    $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                    $session->setPageData($model->getData());
                    $model->save();
                    $this->messageManager->addSuccess(__('You saved the item.'));
                    $session->setPageData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('*/*/');
                    return;
                }else{
                    throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                }

            }catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
            
        }

        $this->_redirect('*/*/');
    }

}
