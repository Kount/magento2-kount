<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Block\Provider;

use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Kount\Kount2FA\Lib\PHPGangsta\GoogleAuthenticator;
use Kount\Kount2FA\Model\GoogleAuthenticatorService;
use Kount\Kount2FA\Model\SecretFactory;

class Google extends Template
{
    const SESSION_KEY = 'google_authentication';

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GoogleAuthenticatorService
     */
    private $googleAuthenticatorService;

    /**
     * @var GoogleAuthenticator
     */
    private $googleAuthenticator;

    /**
     * @var string
     */
    private $googleSecret;

    /**
     * @var SecretFactory
     */
    private $secretFactory;

    /**
     * Authenticator constructor.
     * @param Context $context
     * @param GoogleAuthenticator $googleAuthenticator
     * @param GoogleAuthenticatorService $googleAuthenticatorService
     * @param CatalogSession $session
     * @param Session $customerSession
     * @param SecretFactory $secretFactory
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        GoogleAuthenticator $googleAuthenticator,
        GoogleAuthenticatorService $googleAuthenticatorService,
        CatalogSession $session,
        Session $customerSession,
        SecretFactory $secretFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->googleAuthenticator = $googleAuthenticator;
        if ($secret = $session->getData(self::SESSION_KEY)) {
            $this->googleSecret = $secret;
        } else {
            $this->googleSecret = $this->googleAuthenticator->createSecret();
            $session->setData(self::SESSION_KEY, $this->googleSecret);
        }
        $this->googleAuthenticatorService = $googleAuthenticatorService;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->secretFactory = $secretFactory;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getQrCodeBase64Image()
    {
        // Replace non-alphanumeric characters with dashes; Google Authenticator does not like spaces in the title
        $title = preg_replace('/[^a-z0-9]+/i', '-', $this->storeManager->getWebsite()->getName() . ' 2FA Login');
        $imageData = base64_encode($this->googleAuthenticatorService->getQrCodeEndroid($title, $this->googleSecret));

        return 'data:image/png;base64,' . $imageData;
    }

    /**
     * Returns action url for setup authentication form.
     * @return string
     */
    public function getSetupFormAction()
    {
        return $this->getUrl('kount2fa/account/setup', ['_secure' => true]);
    }

    /**
     * Returns action url for authentication form.
     * @return string
     */
    public function getAuthenticateFormAction()
    {
        return $this->getUrl('kount2fa/account/authenticate', ['_secure' => true]);
    }

    /**
     * @param Customer|null $customer
     * @return bool
     */
    public function is2faConfiguredForCustomer(Customer $customer = null): bool
    {
        if ($customer === null) {
            $customer = $this->customerSession->getCustomer();
        }

        $secret = $this->secretFactory->create()->load($customer->getId(), 'customer_id');

        return ($secret->getId() && $secret->getSecret());
    }

    /**
     * @return string
     */
    public function getSecretCode()
    {
        return $this->googleSecret;
    }

    /**
     * @param $secret
     * @param $code
     * @return bool
     */
    public function authenticateQRCode(string $secret, string $code)
    {
        if (!$secret || !$code) {
            return false;
        }

        return $this->googleAuthenticator->verifyCode($secret, $code);
    }
}
