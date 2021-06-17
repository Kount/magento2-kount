<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Base\Builder;

class Session
{
    /**
     * @var \Kount\Kount\Model\Session
     */
    protected $kountSession;

    /**
     * Session constructor.
     * @param \Kount\Kount\Model\Session $kountSession
     */
    public function __construct(
        \Kount\Kount\Model\Session $kountSession
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
