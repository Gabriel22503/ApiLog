<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Log;
use App\Repository\LogRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class LogController extends AbstractController
{
    #[Route('api/log', name: 'post_log', methods:['POST'])]
    public function addLog(Request $request,  EntityManagerInterface $em, serializerInterface $serializer ): Response
    {
        $client = $em->getRepository(Client::class)->findOneBy(["ip" => $_SERVER["REMOTE_ADDR"], "guid" => $request->get("guid"), "status" => true ]);

        if ($client == null) {
            throw new NotFoundHttpException();
        }
        $log = new Log();

        $log->setClient($client);
        $log->setMessage($request->getContent());
        $em->persist($log);
        $em->flush();


        return new Response("OK");
    }
    #[Route('api/client', name: 'post_client', methods:['POST'])]
    public function addCLient(EntityManagerInterface $em, Request $request ): Response{
        $name = $request->get("nameApp");
        $ip = $request->get("ipApp");

        $client = new CLient();

        $client->setName($name);
        $client->setIp($ip);
        $client->setGuid($name);
        $client->setStatus(true);
        $em->persist($client);
        $em->flush();

        return new Response("OK");
    }
    #[Route('api/logs', name:'get_log', methods:['GET'])]
    public function getLogs(LogRepository $logRepository, SerializerInterface $serializer ) : Response
    {
        $logList = $logRepository->findAll();

        foreach ($logList as $log) {
            error_log($log->getMessage());
        }

        $jsonLogList = $serializer->serialize($logList, 'json', ['groups'=>'getLogs']);

        error_log($jsonLogList);

        //$jsonLogList = $serializer->serialize($logList, 'body', ['groups' => 'getLogs']);

        return new Response($jsonLogList, Response::HTTP_OK, [], );

    }
    /*#[Route('api/appliName', name:'get_appliName', methods:['GET'])]
    public function getAppliName(LogRepository $logRepository, SerializerInterface $serializer, Log $log): JsonResponse
    {

        $appliNameList = $logRepository->find($log->getAppliName());

        $jsonAppliNameList = $serializer->serialize($appliNameList, 'json');

        return new JsonResponse($jsonAppliNameList, Response::HTTP_OK, [], true);

    }*/

    /*#[Route('api/{appliName}', name:'get_appliName', methods:['GET'])]
    public function getLogsByAppliName(string $appliName, LogRepository $logRepository, SerializerInterface $serializer): JsonResponse
    {
        $logList = $logRepository->findBy(["appliName" => $appliName]);
    }*/
}
