<?php

namespace App\Controller;

use App\Dto\User\CreateUserDto;
use App\Dto\User\UpdateUserDto;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Traits\UserAwareTrait;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityLayer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/users')]
#[OA\Tag('User')]
#[Security(name: 'Bearer')]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Not authorized to access this resource',
)]
class UsersController extends AbstractController
{
    use UserAwareTrait;

    public function __construct(
        private readonly UserRepository    $userRepository,
        private readonly CompanyRepository $companyRepository,
        SecurityLayer $security
    )
    {
        $this->setSecurity($security);
    }

    #[Route('/', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all users of the company',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class)),
        )
    )]
    public function index(): JsonResponse
    {
        $companyId = $this->getUserCompanyId();
        $users = $this->userRepository->findAllOfCompany($companyId);

        return $this->json([
            'data' => $users,
        ], Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/{userId}', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'userId',
        description: 'ID of the user you want to find',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the user',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User is not found',
    )]
    public function show(string $userId): JsonResponse
    {
        try {
            $companyId = $this->getUserCompanyId();

            $user = $this->userRepository->findOneOfCompanyById($companyId, $userId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $user], Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns created user',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Company to create the user for is not found',
    )]
    public function create(#[MapRequestPayload] CreateUserDto $createUserDto): JsonResponse
    {
        try {
            $company = $this->companyRepository->findOneById($createUserDto->companyId);

            $user = new User();
            $user->setUsername($createUserDto->username);
            $user->setPassword($createUserDto->password);
            $user->setFirstName($createUserDto->firstName);
            $user->setlastName($createUserDto->lastName);
            $user->setCompany($company);

            $this->userRepository->save($user);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $user], Response::HTTP_CREATED, [], ['groups' => 'user:read']);
    }

    #[Route('/{userId}', methods: ['PUT'], format: 'json')]
    #[OA\Parameter(
        name: 'userId',
        description: 'ID of the user you want to update',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the updated user',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User is not found',
    )]
    public function update(#[MapRequestPayload] UpdateUserDto $updateUserDto, string $userId): JsonResponse
    {
        try {
            $user = $this->userRepository->update($userId, $updateUserDto);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $user], Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    #[Route('/{userId}', methods: ['DELETE'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: '',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User is not found',
    )]
    public function delete(string $userId): JsonResponse
    {
        try {
            $this->userRepository->deleteOneById($userId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json([], Response::HTTP_OK);
    }
}
