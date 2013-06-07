<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
 */

namespace AT\ResourceAccessBundle\Repository;

use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Model\RequesterInterface;
use AT\ResourceAccessBundle\Model\ResourceInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityRepository;

class ResourceAccessRepository extends EntityRepository
{
    public function getAccessLevel(RequesterInterface $requester, ResourceInterface $resource)
    {
        $qb = $this->createQueryBuilder('ra');

        $qb
            ->select('ra')
            ->where('ra.requester = :requester AND ra.resource = :resource')
            ->setParameters(['requester' => $requester, 'resource' => $resource])
        ;

        try {
            /** @var ResourceAccess $result */
            $result = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }

        return $result->getAccessLevels();
    }
}