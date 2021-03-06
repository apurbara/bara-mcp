<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Auth\ClientRegistrantRepository as InterfaceForAuthorization;
use Query\Application\Service\Firm\Client\ProgramRegistrationRepository;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineClientRegistrantRepository extends EntityRepository implements ProgramRegistrationRepository, InterfaceForAuthorization
{

    public function all(string $firmId, string $clientId, int $page, int $pageSize, ?bool $concludedStatus = null)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->leftJoin('programRegistration.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        if (isset($concludedStatus)) {
            $qb->leftJoin('programRegistration.registrant', 'registrant')
                    ->andWhere($qb->expr()->eq('registrant.concluded', ':concludedStatus'))
                    ->setParameter('concludedStatus', $concludedStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $clientId, string $programRegistrationId): ClientRegistrant
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programRegistrationId' => $programRegistrationId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->andWhere($qb->expr()->eq('programRegistration.id', ':programRegistrationId'))
                ->leftJoin('programRegistration.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program registration not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function containRecordOfUnconcludedRegistrationToProgram(string $firmId, string $clientId, string $programId): bool
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("clientRegistrant");
        $qb->select("1")
                ->leftJoin("clientRegistrant.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("clientRegistrant.registrant", "registrant")
                ->andWhere($qb->expr()->eq("registrant.concluded", "false"))
                ->leftJoin("registrant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

}
