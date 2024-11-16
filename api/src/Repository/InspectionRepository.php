<?php

namespace App\Repository;

use App\Dto\Inspection\UpdateInspectionDto;
use App\Entity\Inspection;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Inspection>
 */
class InspectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inspection::class);
    }

    public function findAllOfCar(string $carId)
    {
        return $this->createQueryBuilder('i')
            ->where('i.car = :carId')
            ->setParameter('carId', $carId, UuidType::NAME)
            ->getQuery()
            ->getResult();
    }

    public function findAllOfUser(string $userId)
    {
        return $this->createQueryBuilder('i')
            ->where('i.user = :userId')
            ->setParameter('userId', $userId, UuidType::NAME)
            ->getQuery()
            ->getResult();
    }

    public function findAllOfCompany(string $companyId)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.user', 'u')
            ->where('u.company = :companyId')
            ->setParameter('companyId', $companyId, UuidType::NAME)
            ->getQuery()
            ->getResult();
    }

    public function findOneById(string $id): Inspection
    {
        $inspection = $this->find($id);

        if (!$inspection) {
            throw new NotFoundHttpException('Inspection not found.');
        }

        return $inspection;
    }

    public function save(Inspection $inspection): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($inspection);
        $entityManager->flush();
    }

    public function update(string $id, UpdateInspectionDto $updateDto): Inspection
    {
        $inspection = $this->findOneById($id);
        $entityManager = $this->getEntityManager();

        if ($updateDto->inspectionDate !== null) {
            $inspection->setInspectionDate(new DateTime($updateDto->inspectionDate));
        }

        if (is_int($updateDto->inspectionType)) {
            $inspection->setInspectionType($updateDto->inspectionType);
        }

        $entityManager->flush();

        return $inspection;
    }

    public function deleteOneById(string $id): void
    {
        $inspection = $this->findOneById($id);
        $entityManager = $this->getEntityManager();
        $entityManager->remove($inspection);
        $entityManager->flush();
    }
}
