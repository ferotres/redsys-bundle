<?php

namespace Ferotres\RedsysBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Ferotres\RedsysBundle\Entity\RedsysOrderTrace;

/**
 * Class RedsysTracesRepository
 * @package Ferotres\RedsysBundle\Repository
 */
class RedsysOrderTraceRepository extends EntityRepository implements RedsysOrderTraceRepositoryInterface
{
    /**
     * @param RedsysOrderTrace $redsysOrderTrace
     */
    public function save(RedsysOrderTrace $redsysOrderTrace)
    {
        $this->_em->persist($redsysOrderTrace);
        $this->_em->flush();
    }
}