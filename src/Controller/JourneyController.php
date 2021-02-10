<?php

namespace App\Controller;

use App\Entity\Journey;
use App\Service\JourneyManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JourneyController extends AbstractController
{
    private $manager;
    private $journey;
    private $cardsArray;

    public function __construct(JourneyManager $journeyManager)
    {
        $this->manager = $journeyManager;
        $this->journey = new Journey();
        $this->cardsArray = [];
    }

    /**
     * @Route("/journey", name="journey", methods={"POST"})
     */
    public function index(Request $request): Response
    {
        $this->initProperties($request);

        $jsonContent = $this->manager->getSerializedJourney($this->journey);

        $response = JsonResponse::fromJsonString($jsonContent);

        return $response;
    }

    /**
     * @Route("/journey-text", name="journey-text", methods={"POST"})
     */
    public function indexTest(Request $request): Response
    {
        $this->initProperties($request);

        $textContent = $this->manager->getTextualJourney($this->journey);

        $response = new Response($textContent);

        return $response;
    }

    public function initProperties($request): void
    {
        $this->cardsArray = $this->manager->getCardsArrayFromJson($request->getContent());

        $this->journey = $this->manager->buildJourney($this->cardsArray);
    }
}
