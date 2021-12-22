<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ErrorsFormat",
 *     @OA\Property(property="errors", type="array", description="Errors messages", @OA\Items(type="string")),
 * )
 */
abstract class AbstractApiController extends AbstractController
{
    // Exemple : b952388a-8424-11eb-97c5-0242ac130002
    const UUID_REGEX = '^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$';

    protected function jsonSerialized($data, array $serializedGroups = ['read'], int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        return $this->json($data, $status, $headers, ['groups' => $serializedGroups] + $context);
    }

    protected function noContent(int $status = Response::HTTP_NO_CONTENT, array $headers = []): Response
    {
        return new Response('', $status, $headers);
    }

    /**
     * @param $type Class or object to serialize data in (ex: \StdClass, \StdClass[], $object)
     * @param string $format default: json
     * @param array|string[] $groups serialization groups
     *
     * @return array|mixed|object|JsonResponse the data serialized or the JsonResponse if errors
     */
    protected function getRequestContentDeserialized($type, array $groups = ['write'], string $format = 'json')
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');
        /** @var SerializerInterface $serializer */
        $serializer = $this->get('serializer');
        /** @var Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();

        $context = ['groups' => $groups];

        if (is_object($type)) {
            $context['object_to_populate'] = $type;
            $type = get_class($type);
        }

        try {
            $data = $serializer->deserialize($request->getContent(), $type, $format, $context);
        } catch (ExceptionInterface $exception) {
            throw new \InvalidArgumentException('Invalid data provided', Response::HTTP_BAD_REQUEST, $exception);
        }

        if (($errors = $validator->validate($data))->count() > 0) {
            return $this->json(
                [
                    'errors' => array_map(
                        fn(ConstraintViolation $error) => $error->getMessage(),
                        iterator_to_array($errors)
                    ),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $data;
    }

    public static function getSubscribedServices()
    {
        return parent::getSubscribedServices() + [
            'validator' => '?'.ValidatorInterface::class,
        ];
    }
}