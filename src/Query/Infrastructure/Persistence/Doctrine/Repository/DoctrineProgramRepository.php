<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineProgramRepository extends EntityRepository implements ProgramRepository
{

    public function ofId(string $firmId, string $programId): Program
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programId)
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}