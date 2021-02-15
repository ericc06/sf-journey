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
    private $journeyManager;
    private $journey;
    private $ridesArray;

    public function __construct(JourneyManager $journeyManager)
    {
        $this->journeyManager = $journeyManager;
        $this->journey = new Journey();
        $this->ridesArray = [];
    }

    /**
     * @Route("/journey/get-json", name="journey-json", methods={"POST"})
     */
    public function indexJson(Request $request): Response
    {
        $this->createJourney($request);

        $jsonContent = $this->journeyManager->getSerializedJourney($this->journey);

        $response = JsonResponse::fromJsonString($jsonContent);

        return $response;
    }

    /**
     * @Route("/journey/get-text", name="journey-text", methods={"POST"})
     */
    public function indexText(Request $request): Response
    {
        $this->createJourney($request);

        $textContent = $this->journeyManager->getTextualJourney($this->journey);

        $response = new Response($textContent);

        return $response;
    }

    public function createJourney($request): void
    {
        $this->ridesArray = $this->journeyManager->getRidesArrayFromJson($request->getContent());

        $this->journey = $this->journeyManager->getBuiltJourney($this->ridesArray);

        // Saving the journey to DB and getting the journey with the INSERT id
        $this->journey = $this->journeyManager->persistJourney($this->journey);
    }
}
