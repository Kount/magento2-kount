<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Controller\Adminhtml\Authenticator;

use Kount\Kount2FA\Model\SecretFactory;
use Magento\Backend\App\Action;

class Reset extends Action
{
    /**
     * @var SecretFactory
     */
    private $secretFactory;

    /**
     * Reset constructor.
     * @param Action\Context $context
     * @param SecretFactory $secretFactory
     */
    public function __construct(
        Action\Context $context,
        SecretFactory $secretFactory
    ) {
        parent::__construct($context);
        $this->secretFactory = $secretFactory;
    }

    public function execute()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        $secret = $this->secretFactory->create()->load($customerId, 'customer_id');
        if ($secret->getId()) {
            $secret->delete();
            $this->messageManager->addSuccessMessage(__('Kount2FA for customer has been reset.'));
        } else {
            $this->messageManager->addNoticeMessage(__('Kount2FA for customer has never been set.'));
        }
        $this->_redirect('customer/index/edit', ['id' => $customerId]);
    }
}
