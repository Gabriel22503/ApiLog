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
    public function addLog(Request $request,  EntityManagerInterface $em ): Response
    {
        $client = $em->getRepository(Client::class)->findOneBy(["ip" => $_SERVER["REMOTE_ADDR"], "guid" => $request->get("guid"), "status" => true ]);


        $clients = $em->getRepository(Client::class)->findAll();
        foreach ($clients as $c) {
            error_log($c->getId());
            error_log($c->getIp());
            error_log($c->getGuid());
            error_log($c->isStatus());

            if ($c->getIp() == $_SERVER["REMOTE_ADDR"]) {
                error_log("ip oui");
            } else {
                error_log("ip non");
            }

            if ($c->getGuid() == $request->get("guid")) {
                error_log("guid oui");
            } else {
                error_log("guid non");
            }
        }

        error_log('ici');
        error_log($_SERVER["REMOTE_ADDR"]);
        error_log($request->get("guid"));
        error_log($request->getContent());

        if ($client == null) {
            throw new NotFoundHttpException();
        }
        $log = new Log();

        $log->setClient($client);
        $log->setMessage($request->getContent());
        //$log->setCreatedAt(new \DateTime("now"));
        //$log = $serializer->deserialize($request->getContent(), Log::class, 'json');
        $em->persist($log);
        $em->flush();

        /*$jsonLog = $serializer->serialize($log, 'json', //['groups'=> 'getLogs']
        );*/
        //$location = $urlGenerator->generate('detailLogs', ['id' => $log->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        //return new JsonResponse($jsonLog, Response::HTTP_CREATED);

        return new Response("OK");
    }
    #[Route('api/logs', name:'get_log', methods:['GET'])]
    public function getLogs(LogRepository $logRepository, SerializerInterface $serializer): JsonResponse
    {

        $logList = $logRepository->findAll();

        $jsonLogList = $serializer->serialize($logList, 'json');

        return new JsonResponse($jsonLogList, Response::HTTP_OK, [], true);
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
