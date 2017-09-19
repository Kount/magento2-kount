<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model;

use Swarming\Kount\Api\Data\RisInterface;
use Magento\Framework\Model\AbstractModel;

class Ris extends AbstractModel implements RisInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Swarming\Kount\Model\ResourceModel\Ris::class);
    }

    /**
     * @return int
     */
    public function getRisId()
    {
        return $this->getData(self::RIS_ID);
    }

    /**
     * @param int $risId
     * @return $this
     */
    public function setRisId($risId)
    {
        $this->setData(self::RIS_ID, $risId);
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::ORDER_ID, $orderId);
        return $this;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->getData(self::SCORE);
    }

    /**
     * @param int $score
     * @return $this
     */
    public function setScore($score)
    {
        $this->setData(self::SCORE, $score);
        return $this;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->getData(self::RESPONSE);
    }

    /**
     * @param string $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->setData(self::RESPONSE, $response);
        return $this;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->getData(self::RULE);
    }

    /**
     * @param string $rule
     * @return $this
     */
    public function setRule($rule)
    {
        $this->setData(self::RULE, $rule);
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRAN);
    }

    /**
     * @param string $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        $this->setData(self::TRAN, $transactionId);
        return $this;
    }

    /**
     * @return string
     */
    public function getGeox()
    {
        return $this->getData(self::GEOX);
    }

    /**
     * @param string $geox
     * @return $this
     */
    public function setGeox($geox)
    {
        $this->setData(self::GEOX, $geox);
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->setData(self::COUNTRY, $country);
        return $this;
    }

    /**
     * @return string
     */
    public function getKaptcha()
    {
        return $this->getData(self::KAPTCHA);
    }

    /**
     * @param string $kaptcha
     * @return $this
     */
    public function setKaptcha($kaptcha)
    {
        $this->setData(self::KAPTCHA, $kaptcha);
        return $this;
    }

    /**
     * @return string
     */
    public function getCards()
    {
        return $this->getData(self::CARDS);
    }

    /**
     * @param string $cards
     * @return $this
     */
    public function setCards($cards)
    {
        $this->setData(self::CARDS, $cards);
        return $this;
    }

    /**
     * @return string
     */
    public function getEmails()
    {
        return $this->getData(self::EMAILS);
    }

    /**
     * @param string $emails
     * @return $this
     */
    public function setEmails($emails)
    {
        $this->setData(self::EMAILS, $emails);
        return $this;
    }

    /**
     * @return string
     */
    public function getDevices()
    {
        return $this->getData(self::DEVICES);
    }

    /**
     * @param string $devices
     * @return $this
     */
    public function setDevices($devices)
    {
        $this->setData(self::DEVICES, $devices);
        return $this;
    }
}
