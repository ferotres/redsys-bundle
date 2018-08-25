<?php

namespace Ferotres\RedsysBundle\Repository;
use Ferotres\RedsysBundle\Entity\RedsysOrderTrace;

/**
 * Interface RedsysTracesRepositoryInterface
 * @package Ferotres\RedsysBundle\Repository
 */
interface RedsysOrderTraceRepositoryInterface
{
    /**
     * @param RedsysOrderTrace $redsysOrderTrace
     * @return mixed
     */
    public function save(RedsysOrderTrace $redsysOrderTrace);
}