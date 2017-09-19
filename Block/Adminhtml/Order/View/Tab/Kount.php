<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Block\Adminhtml\Order\View\Tab;

class Kount extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'order/view/tab/kount.phtml';

    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Swarming\Kount\Model\Order\Ris
     */
    protected $orderRis;

    /**
     * @var \Swarming\Kount\Model\Ris
     */
    protected $ris;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Swarming\Kount\Model\Config\Account $configAccount
     * @param \Swarming\Kount\Model\Order\Ris $orderRis
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Swarming\Kount\Model\Config\Account $configAccount,
        \Swarming\Kount\Model\Order\Ris $orderRis,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->configAccount = $configAccount;
        $this->coreRegistry = $coreRegistry;
        $this->orderRis = $orderRis;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->initRis();
        return parent::_prepareLayout();
    }

    protected function initRis()
    {
        $this->ris = $this->orderRis->getRis($this->getOrder());
    }

    /**
     * @return string
     */
    public function getRisScore()
    {
        return $this->ris->getScore() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisResponse()
    {
        return $this->ris->getResponse() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisDescription()
    {
        return $this->ris->getDescription() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisRules()
    {
        return $this->ris->getRule() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisTransactionId()
    {
        return $this->ris->getTransactionId();
    }

    /**
     * @return string
     */
    public function getAWCUrl()
    {
        return $this->configAccount->getAwcUrl($this->getOrder()->getStore()->getWebsiteId())
        . '/workflow/detail.html?id=' . $this->getRisTransactionId();
    }

    /**
     * @return string
     */
    public function getRisGeox()
    {
        return $this->ris->getGeox() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisCountry()
    {
        return $this->ris->getCountry() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisKaptcha()
    {
        return $this->ris->getKaptcha() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisCards()
    {
        return $this->ris->getCards() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisEmails()
    {
        return $this->ris->getEmails() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getRisDevices()
    {
        return $this->ris->getDevices() ? : __('N/A');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Kount');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Kount');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
