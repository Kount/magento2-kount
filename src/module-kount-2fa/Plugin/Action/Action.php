<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Plugin\Action;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Kount\Kount2FA\Model\SecretFactory;

class Action
{
    const KOUNT_KOUNT2FA_GENERAL_ENABLE = 'kount_kount2fa/general/enable';

    const KOUNT_2_FA_ACCOUNT_SETUP_ROUTE = 'kount_kount2fa_account_setup';
    const KOUNT_2_FA_ACCOUNT_AUTHENTICATE_ROUTE = 'kount_kount2fa_account_authenticate';
    const CUSTOMER_ACCOUNT_LOGIN_ROUTE = 'customer_account_login';
    const CUSTOMER_ACCOUNT_LOGOUT_ROUTE = 'customer_account_logout';
    const CUSTOMER_SECTION_LOAD_ROUTE = 'customer_section_load';

    const KOUNT_2_FA_ACCOUNT_SETUP_PATH = 'kount2fa/account/setup';
    const KOUNT_2_FA_ACCOUNT_AUTHENTICATE_PATH = 'kount2fa/account/authenticate';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var SecretFactory
     */
    private $secretFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * Action constructor.
     * @param ScopeConfigInterface $config
     * @param SecretFactory $secretFactory
     * @param Session $customerSession
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ScopeConfigInterface $config,
        SecretFactory $secretFactory,
        Session $customerSession,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager
    ) {
        $this->config = $config;
        $this->secretFactory = $secretFactory;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * @param Action $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return Redirect
     */
    public function aroundDispatch(
        \Magento\Framework\App\Action\Action $subject,
        callable $proceed,
        RequestInterface $request
    ) {
        if ($this->isNeedRedirect($subject->getRequest())) {
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($this->is2faConfiguredForCustomer($this->customerSession->getCustomer())) {
                $resultRedirect->setPath(self::KOUNT_2_FA_ACCOUNT_AUTHENTICATE_PATH);
            } else {
                $this->messageManager->addNoticeMessage(__(
                    'You need to set up Two Factor Authentication before continuing.'
                ));
                $resultRedirect->setPath(self::KOUNT_2_FA_ACCOUNT_SETUP_PATH);
            }

            return $resultRedirect;
        } else {
            return $proceed($request);
        }
    }

    /**
     * Return false if user doesn't need setup or authenticate 2fa
     * @param RequestInterface $request
     * @return bool
     */
    private function isNeedRedirect(RequestInterface $request)
    {
        $needRedirect = true;

        if (!$this->config->isSetFlag(self::KOUNT_KOUNT2FA_GENERAL_ENABLE, ScopeInterface::SCOPE_STORE)) {
            $needRedirect = false;
        }
        if ($this->customerSession->get2faSuccessful()) {
            $needRedirect = false;
        }
        $customer = $this->customerSession->getCustomer();
        if (!$customer->getId() || !$this->customerSession->isLoggedIn()) {
            $needRedirect = false;
        }
        if (in_array($request->getFullActionName(), $this->getAllowedRoutes($customer))) {
            $needRedirect = false;
        }

        return $needRedirect;
    }

    /**
     * Check is customer enter qr-code
     * @param Customer $customer
     * @return bool
     */
    private function is2faConfiguredForCustomer(Customer $customer)
    {
        $secret = $this->secretFactory->create()->load($customer->getId(), 'customer_id');
        if ($secret->getId() && $secret->getSecret()) {
            return true;
        }

        return false;
    }

    /**
     * Get routes that don't need redirection
     * @param Customer $customer
     * @return string[]
     */
    private function getAllowedRoutes(Customer $customer)
    {
        $routes[] = self::CUSTOMER_ACCOUNT_LOGIN_ROUTE;
        $routes[] = self::CUSTOMER_ACCOUNT_LOGOUT_ROUTE;
        $routes[] = self::CUSTOMER_SECTION_LOAD_ROUTE;
        if ($this->is2faConfiguredForCustomer($customer)) {
            $routes[] = self::KOUNT_2_FA_ACCOUNT_AUTHENTICATE_ROUTE;
        } else {
            $routes[] = self::KOUNT_2_FA_ACCOUNT_SETUP_ROUTE;
        }

        return $routes;
    }
}
