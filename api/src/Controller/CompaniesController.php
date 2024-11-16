<?php

namespace App\Controller;

use App\Dto\Company\CreateCompanyDto;
use App\Dto\Company\UpdateCompanyDto;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;


#[Route('/companies')]
#[OA\Tag('Company')]
#[Security(name: 'Bearer')]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Not authorized to access this resource',
)]
class CompaniesController extends AbstractController
{
    public function __construct(private readonly CompanyRepository $companyRepository)
    {
    }

    #[Route('/', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all companies',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Company::class)),
        )
    )]
    public function index(): JsonResponse
    {
        $companies = $this->companyRepository->findAll();

        return $this->json(['data' => $companies]);
    }

    #[Route('/{companyId}', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'companyId',
        description: 'ID of the company you want to find',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the company',
        content: new Model(type: Company::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Company is not found',
    )]
    public function show(string $companyId): JsonResponse
    {
        try {
            $company = $this->companyRepository->findOneById($companyId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $company]);
    }

    #[Route('/', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns created company',
        content: new Model(type: Company::class)
    )]
    public function create(#[MapRequestPayload] CreateCompanyDto $createCompanyDto): JsonResponse
    {
        $company = new Company();
        $company->setName($createCompanyDto->name);

        $this->companyRepository->save($company);

        return $this->json(['data' => $company], Response::HTTP_CREATED);
    }

    #[Route('/{companyId}', methods: ['PUT'], format: 'json')]
    #[OA\Parameter(
        name: 'company',
        description: 'ID of the company you want to update',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the updated company',
        content: new Model(type: Company::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Company is not found',
    )]
    public function update(#[MapRequestPayload] UpdateCompanyDto $updateCompanyDto, string $companyId): JsonResponse
    {
        try {
            $company = $this->companyRepository->update($companyId, $updateCompanyDto);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $company], Response::HTTP_OK);
    }

    #[Route('/{companyId}', methods: ['DELETE'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: '',
        content: new Model(type: Company::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Company is not found',
    )]
    public function delete(string $companyId): JsonResponse
    {
        try {
            $this->companyRepository->deleteOneById($companyId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json([], Response::HTTP_OK);
    }
}
