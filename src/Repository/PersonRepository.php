<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * PersonRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PersonRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Person::class);
    }

    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.fullName like concat('%', :q, '%')");
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    public function fulltextQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect('MATCH (e.fullName) AGAINST (:q BOOLEAN) as HIDDEN score');
        $qb->add('where', 'MATCH (e.fullName) AGAINST (:q BOOLEAN) > 0');
        $qb->orderBy('score', 'desc');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}
