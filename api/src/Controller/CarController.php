<?php

namespace App\Controller;

use App\Dto\Car\CreateCarDto;
use App\Dto\Car\UpdateCarDto;
use App\Entity\Car;
use App\Repository\CarRepository;
use App\Repository\CustomerRepository;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/cars')]
#[OA\Tag('Car')]
#[Security(name: 'Bearer')]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Not authorized to access this resource',
)]
class CarController extends AbstractController
{
    public function __construct(
        private readonly CarRepository      $carRepository,
        private readonly CustomerRepository $customerRepository,
    )
    {
    }

    #[Route('/{customerId}', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'customerId',
        description: 'ID of the customer to return cars for',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all cars of a customer',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Car::class)),
        )
    )]
    public function index(string $customerId): JsonResponse
    {
        $cars = $this->carRepository->findOneBy(['customer' => $customerId], ['carType' => 'ASC']);

        return $this->json(['data' => $cars], Response::HTTP_OK);
    }

    #[Route('/{customerId}/{carId}', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'customerId',
        description: 'ID of the customer to find the car for',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Parameter(
        name: 'carId',
        description: 'ID of the car you want to find',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns car of customer',
        content: new Model(type: Car::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Car is not found',
    )]
    public function show(string $customerId, string $carId): JsonResponse
    {
        try {
            $car = $this->carRepository->findOneOfCustomerById($customerId, $carId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $car], Response::HTTP_OK);
    }

    #[Route('/', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns created car',
        content: new Model(type: Car::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Customer to create the car for is not found',
    )]
    public function create(#[MapRequestPayload] CreateCarDto $createCarDto): JsonResponse
    {
        try {
            $customer = $this->customerRepository->findOneById($createCarDto->customerId);

            $car = new Car();

            $car->setLicensePlate($createCarDto->licensePlate);
            $car->setCarType($createCarDto->carType);
            $car->setCustomer($customer);

            $this->carRepository->save($car);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $car], Response::HTTP_CREATED);
    }

    #[Route('/{carId}', methods: ['PUT'], format: 'json')]
    #[OA\Parameter(
        name: 'carId',
        description: 'ID of the car you want to update',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the updated car',
        content: new Model(type: Car::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Car is not found',
    )]
    public function update(#[MapRequestPayload] UpdateCarDto $updateCarDto, string $carId): JsonResponse
    {
        try {
            $car = $this->carRepository->update($carId, $updateCarDto);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $car], Response::HTTP_OK);
    }

    #[Route('/{carId}', methods: ['DELETE'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: '',
        content: new Model(type: Car::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Car is not found',
    )]
    public function delete(string $carId): JsonResponse
    {
        try {
            $this->carRepository->deleteOneById($carId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json([], Response::HTTP_OK);
    }
}
