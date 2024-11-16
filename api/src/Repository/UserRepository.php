<?php

namespace App\Repository;

use App\Dto\User\UpdateUserDto;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function save(User $user): void
    {
        $entityManager = $this->getEntityManager();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    public function update(string $id, UpdateUserDto $updateDto): User
    {
        $user = $this->findOneById($id);
        $entityManager = $this->getEntityManager();

        if (is_string($updateDto->username)) {
            $user->setUsername($updateDto->username);
        }

        if (is_string($updateDto->firstName)) {
            $user->setFirstName($updateDto->firstName);
        }

        if (is_string($updateDto->lastName)) {
            $user->setLastName($updateDto->lastName);
        }

        $entityManager->flush();

        return $user;
    }

    public function findAllOfCompany(string $companyId)
    {
        return $this->createQueryBuilder('u')
            ->where('u.company = :companyId')
            ->orderBy('u.lastName', 'ASC')
            ->orderBy('u.firstName', 'ASC')
            ->setParameter('companyId', $companyId, UuidType::NAME)
            ->getQuery()
            ->getResult();
    }

    public function findOneOfCompanyById(string $companyId, string $userId): User
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.company = :companyId')
            ->andWhere('u.id = :userId')
            ->setParameter('companyId', $companyId, UuidType::NAME)
            ->setParameter('userId', $userId, UuidType::NAME)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }

    public function findOneById(string $id): ?User
    {
        $user = $this->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }

    public function deleteOneById(string $id): void
    {
        $user = $this->findOneById($id);
        $entityManager = $this->getEntityManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }
}
