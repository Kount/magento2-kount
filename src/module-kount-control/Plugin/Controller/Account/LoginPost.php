<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Plugin\Controller\Account;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;

class LoginPost
{
    /**
     * @var \Kount\KountControl\Model\CustomerLogin
     */
    private $customerLogin;

    /**
     * @var \Kount\Kount\Model\Logger
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $httpResponse;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @param \Kount\KountControl\Model\CustomerLogin $customerLogin
     * @param \Kount\Kount\Model\Logger $logger
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Response\Http $httpResponse
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Kount\KountControl\Model\CustomerLogin $customerLogin,
        \Kount\Kount\Model\Logger $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http $httpResponse,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->customerLogin = $customerLogin;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->httpResponse = $httpResponse;
        $this->url = $url;
    }

    /**
     * Initiates API request to Kount system about customer log in. Logs out customer if API response is negative.
     *
     * @param HttpPostActionInterface $httpPostAction
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(HttpPostActionInterface $httpPostAction, Redirect $result)
    {
        $this->customerSession->set2faSuccessful(true);
        $sessionId = '';
        $isSuccessful = true;
        $isChallenge = false;
        if (isset($httpPostAction->getRequest()->getParams()['kountsessionid'])) {
            $sessionId = $httpPostAction->getRequest()->getParams()['kountsessionid'];
            $this->customerSession->setKountSessionId($sessionId);
        }
        // Start work with Login API and Event API
        try {
            $this->customerLogin->login($sessionId);
          // Exit from API workflow if KountControl not configured properly or got "Allow" Login API decision
        } catch (
            \Kount\KountControl\Exception\ConfigException
            | \Kount\KountControl\Exception\PositiveApiResponse $e
        ) {
            $this->logger->info($e->getMessage());
          // Exit from API workflow if it not has all required params for API call or got "Block" Login API decision
        } catch (
            \Kount\KountControl\Exception\ParamsException
            | \Kount\KountControl\Exception\NegativeApiResponse $e
        ) {
            $isSuccessful = false;
            // Log out customer in this case
            $this->logoutCustomer();
            $this->logger->warning($e->getMessage());
          // Exit from API workflow if it got "Challenge" Login API decision and need 2FA
        } catch (
            \Kount\KountControl\Exception\ChallengeApiResponse $e
        ) {
            $isChallenge = true;
            $this->logger->info($e->getMessage());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error(__('KountControl: ' . $e->getMessage()));
        }

        // Specifies the need for 2FA
        if ($isChallenge) {
            $this->customerSession->set2faSuccessful(false);
            return $this->httpResponse->setRedirect($this->url->getUrl('customer/account'));
        }
        // Redirect to customer login page in case of failed API call
        if (!$isSuccessful) {
            return $this->httpResponse->setRedirect($this->url->getUrl('customer/account/login'));
        } else {
            return $result;
        }
    }

    /**
     * @return void
     */
    public function logoutCustomer()
    {
        if ($this->customerSession->getCustomer()->getId()) {
            $this->customerSession->destroy();
        }
    }
}
