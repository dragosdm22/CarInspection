<?php

namespace App\Controller;

use App\Dto\Customer\CreateCustomerDto;
use App\Dto\Customer\UpdateCustomerDto;
use App\Entity\Customer;
use App\Repository\CompanyRepository;
use App\Repository\CustomerRepository;
use App\Traits\UserAwareTrait;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityLayer;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/customers')]
#[OA\Tag('Customer')]
#[Security(name: 'Bearer')]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Not authorized to access this resource',
)]
class CustomerController extends AbstractController
{
    use UserAwareTrait;

    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly CompanyRepository $companyRepository,
        SecurityLayer $security,
    )
    {
        $this->setSecurity($security);
    }

    #[Route('/', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all customers of the company',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Customer::class)),
        )
    )]
    public function index(): JsonResponse
    {
        $companyId = $this->getUserCompanyId();
        $customers = $this->customerRepository->findAllOfCompany($companyId);

        return $this->json(['data' => $customers], Response::HTTP_OK);
    }

    #[Route('/{customerId}', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'customerId',
        description: 'ID of the customer you want to find',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the customer',
        content: new Model(type: Customer::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Customer is not found',
    )]
    public function show(string $customerId): JsonResponse
    {
        try {
            $companyId = $this->getUserCompanyId();

            $customer = $this->customerRepository->findOneOfCompanyById($companyId, $customerId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $customer], Response::HTTP_OK);
    }

    #[Route('/', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns created customer',
        content: new Model(type: Customer::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Company to create the customer for is not found',
    )]
    public function create(#[MapRequestPayload] CreateCustomerDto $createCustomerDto): JsonResponse
    {
        try {
            $company = $this->companyRepository->findOneById($createCustomerDto->companyId);

            $customer = new Customer();
            $customer->setPhoneNumber($createCustomerDto->phoneNumber);

            if (is_string($createCustomerDto->firstName)) {
                $customer->setFirstName($createCustomerDto->firstName);
            }

            if (is_string($createCustomerDto->lastName)) {
                $customer->setLastName($createCustomerDto->lastName);
            }

            $customer->setCompany($company);

            $this->customerRepository->save($customer);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $customer], Response::HTTP_CREATED);
    }

    #[Route('/{customerId}', methods: ['PUT'], format: 'json')]
    #[OA\Parameter(
        name: 'customerId',
        description: 'ID of the customer you want to update',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the updated customer',
        content: new Model(type: Customer::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Customer is not found',
    )]
    public function update(#[MapRequestPayload] UpdateCustomerDto $updateCustomerDto, string $customerId): JsonResponse
    {
        try {
            $customer = $this->customerRepository->update($customerId, $updateCustomerDto);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $customer], Response::HTTP_OK);
    }

    #[Route('/{customerId}', methods: ['DELETE'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: '',
        content: new Model(type: Customer::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Customer is not found',
    )]
    public function delete(string $customerId): JsonResponse
    {
        try {
            $this->customerRepository->deleteOneById($customerId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json([], Response::HTTP_OK);
    }
}
