<?php

namespace App\Repository;

use App\Dto\Customer\UpdateCustomerDto;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findAllOfCompany(string $companyId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.company = :companyId')
            ->setParameter('companyId', $companyId, UuidType::NAME)
            ->getQuery()
            ->getResult();
    }

    public function findOneOfCompanyById(string $companyId, string $customerId): Customer
    {
        $customer = $this->createQueryBuilder('c')
            ->where('c.company = :companyId')
            ->andWhere('c.id = :customerId')
            ->setParameter('companyId', $companyId, UuidType::NAME)
            ->setParameter('customerId', $customerId, UuidType::NAME)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found');
        }

        return $customer;
    }

    public function save(Customer $customer): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($customer);
        $entityManager->flush();
    }

    public function update(string $id, UpdateCustomerDto $updateDto): Customer
    {
        $customer = $this->findOneById($id);
        $entityManager = $this->getEntityManager();

        if (is_string($updateDto->phoneNumber)) {
            $customer->setPhoneNumber($updateDto->phoneNumber);
        }

        if (is_string($updateDto->firstName)) {
            $customer->setFirstName($updateDto->firstName);
        }

        if (is_string($updateDto->lastName)) {
            $customer->setLastName($updateDto->lastName);
        }

        $entityManager->flush();

        return $customer;
    }

    public function findOneById(string $id): ?Customer
    {
        $customer = $this->find($id);

        if (!$customer) {
            throw new NotFoundHttpException('User not found.');
        }

        return $customer;
    }

    public function findByName(string $firstName): Customer
    {
        $customer = $this->findOneBy(['firstName' => $firstName]);

        if (!$customer instanceof Customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        return $customer;
    }

    public function deleteOneById(string $id): void
    {
        $customer = $this->findOneById($id);
        $entityManager = $this->getEntityManager();
        $entityManager->remove($customer);
        $entityManager->flush();
    }
}
