<?php

namespace App\Repository;

use App\Dto\Company\UpdateCompanyDto;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $company): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($company);
        $entityManager->flush();
    }

    public function update(string $id, UpdateCompanyDto $updateDto): Company
    {
        $company = $this->findOneById($id);
        $entityManager = $this->getEntityManager();

        if (is_string($updateDto->name)) {
            $company->setName($updateDto->name);
        }

        $entityManager->flush();

        return $company;
    }

    public function findOneById(string $id): Company
    {
        $company = $this->find($id);

        if (!$company instanceof Company) {
            throw new NotFoundHttpException('Company not found.');
        }

        return $company;
    }

    public function findByName(string $name): Company
    {
        $company = $this->findOneBy(['name' => $name]);

        if (!$company instanceof Company) {
            throw new NotFoundHttpException('Company not found.');
        }

        return $company;
    }

    public function deleteOneById(string $id): void
    {
        $company = $this->findOneById($id);
        $entityManager = $this->getEntityManager();
        $entityManager->remove($company);
        $entityManager->flush();
    }
}
