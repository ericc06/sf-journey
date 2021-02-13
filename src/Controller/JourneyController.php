<?php

namespace App\Controller;

use App\Entity\Journey;
use App\Entity\Trip;
use App\Entity\Card;
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
     * @Route("/journey/get-json", name="journey-json", methods={"POST"})
     */
    public function indexJson(Request $request): Response
    {
        if (strlen($request->getContent()) === 0) {
            return new JsonResponse(['warning' => 'No content received.'], 200);
        }
        
        $this->createJourney($request->getContent());

        $jsonContent = $this->manager->getSerializedJourney($this->journey);

        return JsonResponse::fromJsonString($jsonContent);
    }

    /**
     * @Route("/journey/get-text", name="journey-text", methods={"POST"})
     */
    public function indexText(Request $request): Response
    {
        if (strlen($request->getContent()) === 0) {
            return new Response('No content received.', 200);
        }

        $this->createJourney($request->getContent());

        $textContent = $this->manager->getTextualJourney($this->journey);

        return new Response($textContent);
    }

    public function createJourney($requestContent): void
    {
        $this->cardsArray = $this->manager->getCardsArrayFromJson($requestContent);
        $this->journey = $this->manager->getBuiltJourney($this->cardsArray);

        // Saving the journey to DB and getting the journey with the INSERT id
        $this->journey = $this->manager->persistJourney($this->journey);
    }
}
