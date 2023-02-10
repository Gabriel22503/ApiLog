<?php

namespace App\Controller;

use App\Entity\Log;
use App\Repository\LogRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class LogController extends AbstractController
{
    #[Route('api/log', name: 'post_log', methods:['POST'])]
    public function addLog(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator ): JsonResponse
    {
        $log = $serializer->deserialize($request->getContent(), Log::class, 'json');
        $em->persist($log);
        $em->flush();

        $jsonLog = $serializer->serialize($log, 'json', //['groups'=> 'getLogs']
        );
        //$location = $urlGenerator->generate('detailLogs', ['id' => $log->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonLog, Response::HTTP_CREATED);
    }
    #[Route('api/logs', name:'get_log', methods:['GET'])]
    public function getLogs(LogRepository $logRepository, SerializerInterface $serializer): JsonResponse
    {

        $logList = $logRepository->findAll();

        $jsonLogList = $serializer->serialize($logList, 'json');

        return new JsonResponse($jsonLogList, Response::HTTP_OK, [], true);
    }
}
