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
use Magento\Framework\View\LayoutFactory;
use Kount\Kount2FA\Model\SecretFactory;
use Magento\Framework\Controller\ResultFactory;
use Kount\KountControl\Helper\Config;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

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
     * @param LayoutFactory $layoutFactory
     * @param SecretFactory $secretFactory
     * @param Config $kountControlConfig
     * @param ResultPageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        LayoutFactory $layoutFactory,
        SecretFactory $secretFactory,
        Config $kountControlConfig,
        ResultPageFactory $resultPageFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->secretFactory = $secretFactory;
        $this->kountControlConfig = $kountControlConfig;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|ResultPageFactory
     * @throws \Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $resultPage = $this->resultPageFactory->create();
            return $resultPage;
        } else {
            $authenticator = $this->layoutFactory->create()->createBlock('Kount\Kount2FA\Block\Provider\Google');
            if ($authenticator->authenticateQRCode($post['secret'], $post['code'])) {
                $this->messageManager->addSuccessMessage(__('2FA successfully set up'));
                $this->secretFactory->create()->setData([
                    'customer_id' => $this->customerSession->getCustomerId(),
                    'secret'      => $authenticator->getSecretCode(),
                ])->save();
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
                    $result->setPath('kount2fa/account/setup');

                    return $result;
                }
            }
        }
    }
}
