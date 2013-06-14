<?php

/*
 * This file is part of the ResourceAccessBundle.
 *
 * (c) Theodor Diaconu <diaconu.theodor@gmail.com>
 * (c) Alexandru Miron <beliveyourdream@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AT\ResourceAccessBundle\Repository;

use AT\ResourceAccessBundle\Entity\ResourceAccess;
use AT\ResourceAccessBundle\Model\RequesterInterface;
use AT\ResourceAccessBundle\Model\ResourceInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityRepository;

class ResourceAccessRepository extends EntityRepository
{
    public function getAccessLevels(RequesterInterface $requester, ResourceInterface $resource)
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