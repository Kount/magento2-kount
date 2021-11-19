<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Kount\Kount2FA\Lib\PHPGangsta\GoogleAuthenticator;
use Kount\Kount2FA\Model\SecretFactory;
use Magento\Framework\Controller\ResultFactory;
use Kount\KountControl\Helper\Config;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

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
     * @var Config
     */
    private $kountControlConfig;

    /**
     * @var ResultPageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param GoogleAuthenticator $googleAuthenticator
     * @param SecretFactory $secretFactory
     * @param Config $kountControlConfig
     * @param ResultPageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        GoogleAuthenticator $googleAuthenticator,
        SecretFactory $secretFactory,
        Config $kountControlConfig,
        ResultPageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
        $this->googleAuthenticator = $googleAuthenticator;
        $this->secretFactory = $secretFactory;
        $this->kountControlConfig = $kountControlConfig;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|ResultPageFactory
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setHeader('Login-Required', 'true');
            return $resultPage;
        } else {
            $secret = $this->secretFactory->create()->load(
                $this->customerSession->getCustomerId(),
                'customer_id'
            )->getSecret();
            if ($this->authenticateQRCode($secret, $post['code'])) {
                $this->messageManager->addSuccessMessage(__('Two Factor Authentication successful'));
                $this->customerSession->set2faSuccessful(true);
                $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $result->setPath('customer/account');

                return $result;
            } else {
                $this->customerSession->set2faAttemptCount($this->customerSession->get2faAttemptCount() + 1);
                if (
                    $this->customerSession->get2faAttemptCount()
                    >= $this->kountControlConfig->get2faFailedAttemptsAmount()
                ) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Invalid 2FA Authentication Code. You have spent all possible tries to enter valid 2FA '
                            . 'code. Please log in again!'
                        )
                    );
                    $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $result->setPath('customer/account/login');

                    return $result;
                } else {
                    $this->messageManager->addErrorMessage(
                        __('Invalid 2FA Authentication Code')
                    );
                    $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $result->setPath('kount2fa/account/authenticate');

                    return $result;
                }
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
