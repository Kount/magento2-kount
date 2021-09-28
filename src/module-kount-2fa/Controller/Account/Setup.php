<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\LayoutFactory;
use Kount\Kount2FA\Model\SecretFactory;

class Setup extends Action
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var SecretFactory
     */
    private $secretFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @param Context       $context
     * @param LayoutFactory $layoutFactory
     * @param Session       $customerSession
     * @param SecretFactory $secretFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        LayoutFactory $layoutFactory,
        SecretFactory $secretFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->secretFactory = $secretFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->set(__('Two-Factor Authentication Setup'));
            $this->_view->renderLayout();
        } else {
            $authenticator = $this->layoutFactory->create()->createBlock('Kount\Kount2FA\Block\Provider\Google');
            if ($authenticator->authenticateQRCode($post['secret'], $post['code'])) {
                $this->messageManager->addSuccessMessage(__('2FA successfully set up'));
                $this->secretFactory->create()->setData([
                    'customer_id' => $this->customerSession->getCustomerId(),
                    'secret'      => $authenticator->getSecretCode(),
                ])->save();
                $this->customerSession->set2faSuccessful(true);
                $this->_redirect('customer/account');
            } else {
                $this->messageManager->addErrorMessage(
                    __('Invalid 2FA Authentication Code')
                );
                $this->customerSession->set2faAttemptCount($this->customerSession->get2faAttemptCount() + 1);
                $this->_redirect('kount2fa/account/setup');
            }
        }
    }
}
