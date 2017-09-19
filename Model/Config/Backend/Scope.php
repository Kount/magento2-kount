<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Config\Backend;

class Scope extends \Magento\Config\Model\Config\ScopeDefiner
{
    /**
     * @return int|null
     */
    public function getScopeValue()
    {
        return $this->_request->getParam($this->getScope());
    }
}
