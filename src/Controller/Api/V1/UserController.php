<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Enum\UserRoleEnum;
use App\Repository\UserRepository;
use App\Util\Helpers\ApiResponse;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/v1/users', name: 'app_api_v1_users_')]
#[IsGranted(UserRoleEnum::API->value)]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'List all users',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of users (uuid + email)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '01933b6e-7c1a-7d2f-9a4b-3f8e2c1d5a60'),
                                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function list(): Response
    {
        $users = array_map(
            fn (User $user): array => [
                'uuid' => $user->getUuid()->toRfc4122(),
                'email' => $user->getEmail(),
            ],
            $this->userRepository->findAll()
        );

        return ApiResponse::success('OK', $users);
    }

    #[Route('/{uuid:user}', name: 'show', requirements: ['uuid' => Requirement::UUID_V7], methods: ['GET'])]
    #[OA\Get(
        summary: 'Get user profile by uuid',
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User profile',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '01933b6e-7c1a-7d2f-9a4b-3f8e2c1d5a60'),
                                new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                                new OA\Property(
                                    property: 'roles',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'ROLE_API')
                                ),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function show(User $user): Response
    {
        $roles = array_map(function (string $role): string {
            $enum = UserRoleEnum::tryFrom($role);

            return $enum?->trans($this->translator) ?? $role;
        }, $user->getRoles());

        return ApiResponse::success('OK', [
            'uuid' => $user->getUuid()->toRfc4122(),
            'email' => $user->getEmail(),
            'roles' => $roles,
        ]);
    }
}
