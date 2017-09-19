<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Base\Builder;

class Session
{
    /**
     * @var \Swarming\Kount\Model\Session
     */
    protected $kountSession;

    /**
     * Session constructor.
     * @param \Swarming\Kount\Model\Session $kountSession
     */
    public function __construct(
        \Swarming\Kount\Model\Session $kountSession
    ) {
        $this->kountSession = $kountSession;
    }

    /**
     * @param \Kount_Ris_Request $request
     */
    public function process(\Kount_Ris_Request $request)
    {
        $request->setSessionId($this->kountSession->getKountSessionId());
    }
}
