<?php

namespace App\Repository;

use App\Dto\Car\UpdateCarDto;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function save(Car $car): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($car);
        $entityManager->flush();
    }

    public function update(string $id, UpdateCarDto $updateDto): Car
    {
        $car = $this->findOneById($id);
        $entityManager = $this->getEntityManager();

        if (is_int($updateDto->carType)) {
            $car->setCarType($updateDto->carType);
        }

        if (is_string($updateDto->licensePlate)) {
            $car->setLicensePlate($updateDto->licensePlate);
        }

        $entityManager->flush();

        return $car;
    }

    public function findOneOfCustomerById(string $customerId, string $carId): Car
    {
        $car = $this->createQueryBuilder('c')
            ->where('c.id = :carId')
            ->andWhere('c.customer = :customerId')
            ->setParameter('customerId', $customerId, UuidType::NAME)
            ->setParameter('carId', $carId, UuidType::NAME)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$car) {
            throw new NotFoundHttpException('Car not found.');
        }

        return $car;
    }

    public function findOneById(string $id): ?Car
    {
        $car = $this->find($id);

        if (!$car) {
            throw new NotFoundHttpException('Car not found.');
        }

        return $car;
    }

    public function deleteOneById(string $id): void
    {
        $car = $this->findOneById($id);
        $entityManager = $this->getEntityManager();
        $entityManager->remove($car);
        $entityManager->flush();
    }
}
