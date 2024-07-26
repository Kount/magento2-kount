<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Lib;

class NopLogger extends \Kount_Log_Binding_NopLogger
{
    /**
     * @param string $name
     */
    public function __construct($name = 'nop') {
        parent::__construct($name);
    }
}
