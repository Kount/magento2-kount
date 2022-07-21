<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
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
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @param \Kount\KountControl\Model\CustomerLogin $customerLogin
     * @param \Kount\Kount\Model\Logger $logger
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Response\Http $httpResponse
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Kount\KountControl\Model\CustomerLogin $customerLogin,
        \Kount\Kount\Model\Logger $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http $httpResponse,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->customerLogin = $customerLogin;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->httpResponse = $httpResponse;
        $this->url = $url;
        $this->messageManager = $messageManager;
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

        if ($sessionId !== '' && ($this->customerSession->getCustomer()->getId() !== null)) {
            // Start work with Login API and Event API
            try {
                $this->customerLogin->login($sessionId);
            } catch (
                \Kount\KountControl\Exception\ConfigException
             | \Kount\KountControl\Exception\PositiveApiResponse $e
            ) {
                // Exit from API workflow if KountControl not configured properly or got "Allow" Login API decision
                $this->logger->info($e->getMessage());
            } catch (
                \Kount\KountControl\Exception\NegativeApiResponse $e
            ) {
                // Exit from API workflow if it not has all required params for API call or got "Block" Login API decision
                $isSuccessful = false;
                // Log out customer in this case
                $this->logoutCustomer();
                $this->messageManager->addErrorMessage(__("The sign-in is not available for your customer account."));
                $this->logger->warning($e->getMessage());
            } catch (
                \Kount\KountControl\Exception\ParamsException $e
            ) {
                // Exit from API workflow if it not has all required params for API call
                $isSuccessful = false;
                // Log out customer in this case
                $this->logoutCustomer();
                $this->logger->warning($e->getMessage());
            } catch (
                \Kount\KountControl\Exception\ChallengeApiResponse $e
            ) {
                // Exit from API workflow if it got "Challenge" Login API decision and need 2FA
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
        } else {
            $this->logger->error(__('KountControl: "kountsessionid" not set.'));
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
