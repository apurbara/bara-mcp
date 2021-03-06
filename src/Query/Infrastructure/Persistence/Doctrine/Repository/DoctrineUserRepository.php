<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\User\UserRepository as InterfaceForUser;
use Query\Application\Service\UserRepository;
use Query\Domain\Model\User;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineUserRepository extends EntityRepository implements UserRepository, InterfaceForUser
{

    public function all(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('user');
        $qb->select('user');

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofEmail(string $email): User
    {
        $params = [
            'email' => $email,
        ];

        $qb = $this->createQueryBuilder('user');
        $qb->select('user')
                ->andWhere($qb->expr()->eq('user.email', ':email'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $userId): User
    {
        $params = [
            'userId' => $userId,
        ];

        $qb = $this->createQueryBuilder('user');
        $qb->select('user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user not found';
        }
    }

}
