<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Kount\Kount2FA\Model\SecretFactory;

class Reset2FAGoogle extends GenericButton implements ButtonProviderInterface
{
    const ADMIN_AUTHENTICATOR_RESET_PATH = 'kount2fa/authenticator/reset';

    /**
     * @var SecretFactory
     */
    private $secretFactory;

    /**
     * ResetButton constructor.
     * @param Context $context
     * @param Registry $registry
     * @param UrlInterface $urlBuilder
     * @param SecretFactory $secretFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UrlInterface $urlBuilder,
        SecretFactory $secretFactory
    ) {
        parent::__construct($context, $registry);
        $this->urlBuilder = $urlBuilder;
        $this->secretFactory = $secretFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $secret = $this->secretFactory->create()->load($this->getCustomerId(), 'customer_id');

        if ($secret->getId()) {
            $url = $this->urlBuilder->getUrl(self::ADMIN_AUTHENTICATOR_RESET_PATH, [
                'customer_id' => $this->getCustomerId(),
            ]);
            $data = [
                'label'      => __('Reset Google 2FA code'),
                'on_click'   => sprintf('location.href = "%s";', $url),
                'class'      => 'add',
                'sort_order' => 40,
            ];
        }

        return $data;
    }
}
