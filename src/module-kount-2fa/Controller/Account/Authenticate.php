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
use Kount\Kount2FA\Lib\PHPGangsta\GoogleAuthenticator;
use Kount\Kount2FA\Model\SecretFactory;

class Authenticate extends Action
{
    /**
     * @var GoogleAuthenticator
     */
    private $googleAuthenticator;

    /**
     * @var SecretFactory
     */
    private $secretFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * Authenticate constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param GoogleAuthenticator $googleAuthenticator
     * @param SecretFactory $secretFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        GoogleAuthenticator $googleAuthenticator,
        SecretFactory $secretFactory
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
        $this->googleAuthenticator = $googleAuthenticator;
        $this->secretFactory = $secretFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->set(__('Two-Factor Authentication'));
            $this->_view->renderLayout();
        } else {
            $secret = $this->secretFactory->create()->load(
                $this->customerSession->getCustomerId(),
                'customer_id'
            )->getSecret();
            if ($this->authenticateQRCode($secret, $post['code'])) {
                $this->messageManager->addSuccessMessage(__('Two Factor Authentication successful'));
                $this->customerSession->set2faSuccessful(true);
                $this->_redirect('customer/account');
            } else {
                $this->messageManager->addErrorMessage(__('Two Factor Authentication code incorrect'));
                $this->customerSession->set2faSuccessful(false);
                $this->customerSession->set2faAttemptCount($this->customerSession->get2faAttemptCount() + 1);
                $this->_redirect('*/*/*');
            }
        }
    }

    /**
     * @param string $secret
     * @param string $code
     * @param int $clockTolerance
     * @return bool
     */
    private function authenticateQRCode(string $secret, string $code, int $clockTolerance = 2)
    {
        if (!$secret || !$code) {
            return false;
        }

        return $this->googleAuthenticator->verifyCode($secret, $code, $clockTolerance);
    }
}
