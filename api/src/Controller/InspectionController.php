<?php

namespace App\Controller;

use App\Dto\Inspection\CreateInspectionDto;
use App\Dto\Inspection\UpdateInspectionDto;
use App\Entity\Inspection;
use App\Enums\Inspection\InspectionModelType;
use App\Repository\CarRepository;
use App\Repository\InspectionRepository;
use App\Repository\UserRepository;
use App\Strategies\Inspection\CarInspectionFetcher;
use App\Strategies\Inspection\UserInspectionFetcher;
use App\Traits\UserAwareTrait;
use DateTime;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityLayer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/inspections')]
#[OA\Tag('Inspection')]
#[Security(name: 'Bearer')]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Not authorized to access this resource',
)]
class InspectionController extends AbstractController
{
    use UserAwareTrait;

    public function __construct(
        private readonly InspectionRepository $inspectionRepository,
        private readonly UserRepository       $userRepository,
        private readonly CarRepository        $carRepository,
        SecurityLayer                         $security,
    )
    {
        $this->setSecurity($security);
    }

    #[Route('/', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all inspections of the company',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Inspection::class)),
        )
    )]
    public function index(): JsonResponse
    {
        $companyId = $this->getUserCompanyId();
        $inspections = $this->inspectionRepository->findAllOfCompany($companyId);

        return $this->json(['data' => $inspections], Response::HTTP_OK);
    }

    #[Route('/{modelType}/{modelId}', requirements: ['modelType' => InspectionModelType::REGEX], methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'modelType',
        description: 'Can either be car or user',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Parameter(
        name: 'modelId',
        description: 'ID of the car or user you want to see the inspections for',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all inspections of the model (car, user)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Inspection::class)),
        )
    )]
    public function indexOfModel(string $modelType, string $modelId): JsonResponse
    {
        if ($modelType === InspectionModelType::CAR->value) {
            $fetcher = new CarInspectionFetcher($this->inspectionRepository);
        } else {
            $fetcher = new UserInspectionFetcher($this->inspectionRepository);
        }

        $inspections = $fetcher->findInspections($modelId);

        return $this->json(['data' => $inspections], Response::HTTP_OK);
    }

    #[Route('/{inspectionId}', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'inspectionId',
        description: 'ID of the inspection you want to find',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the inspection',
        content: new Model(type: Inspection::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Inspection is not found',
    )]
    public function show(string $inspectionId): JsonResponse
    {
        try {
            $inspection = $this->inspectionRepository->findOneById($inspectionId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $inspection], Response::HTTP_OK);
    }

    #[Route('/', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Returns created inspection',
        content: new Model(type: Inspection::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Car or the user to create the inspection for is not found',
    )]
    public function create(#[MapRequestPayload] CreateInspectionDto $createInspectionDto): JsonResponse
    {
        try {
            $car = $this->carRepository->findOneById($createInspectionDto->carId);
            $user = $this->userRepository->findOneById($createInspectionDto->userId);

            $inspection = new Inspection();
            $inspection->setInspectionDate(new DateTime($createInspectionDto->inspectionDate));
            $inspection->setInspectionType($createInspectionDto->inspectionType);
            $inspection->setCar($car);
            $inspection->setUser($user);

            $this->inspectionRepository->save($inspection);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $inspection], Response::HTTP_CREATED);
    }

    #[Route('/{inspectionId}', methods: 'PUT', format: 'json')]
    #[OA\Parameter(
        name: 'inspectionId',
        description: 'ID of the inspection you want to update',
        in: 'path',
        schema: new OA\Schema(type: 'UUID')
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the updated inspection',
        content: new Model(type: Inspection::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Inspection is not found',
    )]
    public function update(
        #[MapRequestPayload] UpdateInspectionDto $updateInspectionDto,
        string $inspectionId
    ): JsonResponse
    {
        try {
            $inspection = $this->inspectionRepository->update($inspectionId, $updateInspectionDto);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $inspection], Response::HTTP_OK);
    }

    #[Route('/{inspectionId}', methods: ['DELETE'], format: 'json')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: '',
        content: new Model(type: Inspection::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Inspection is not found',
    )]
    public function delete(string $inspectionId): JsonResponse
    {
        try {
            $this->inspectionRepository->deleteOneById($inspectionId);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json([], Response::HTTP_OK);
    }
}
