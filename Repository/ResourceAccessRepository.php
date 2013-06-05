<?php

/**
 * @author Theodor Diaconu <diaconu.theodor@gmail.com>
 * @author Alexandru Miron <beliveyourdream@gmail.com>
 */

namespace AB\ReviewBundle\Repository;

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
            ->select('ra.accessLevel')
            ->where('ra.requester = :requester AND ra.resource = :resource')
            ->setParameters(['requester' => $requester, 'resource' => $resource])
        ;

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }

        return $result;
    }
}