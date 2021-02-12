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
     * @Route("/repotest", name="repotest", methods={"GET"})
     */
    public function test(): Response
    {
        $cards = $this->getDoctrine()
            ->getRepository(Card::class)
            ->findAllLongerThanGivenHours(40);

        dump($cards);

        /*$trips = $this->getDoctrine()
            ->getRepository(Trip::class)
            ->findImmediateNextTripStartingAfterNow();
            //->findByTripStartDateAfterGivenDate("2021-02-15T22:00:00+00:00");

        dump($trips);*/

        /*$journeys = $this->getDoctrine()
            ->getRepository(Journey::class)
            ->findAllHavingMoreThanGivenNumberOfTrips(2);

        dump($journeys);*/

        exit;
    }

    /**
     * @Route("/journey", name="journey", methods={"POST"})
     */
    public function index(Request $request): Response
    {
        $this->createJourney($request);

        $jsonContent = $this->manager->getSerializedJourney($this->journey);

        $response = JsonResponse::fromJsonString($jsonContent);

        return $response;
    }

    /**
     * @Route("/journey-text", name="journey-text", methods={"POST"})
     */
    public function indexTest(Request $request): Response
    {
        $this->createJourney($request);

        $textContent = $this->manager->getTextualJourney($this->journey);

        $response = new Response($textContent);

        return $response;
    }

    public function createJourney($request): void
    {
        $this->cardsArray = $this->manager->getCardsArrayFromJson($request->getContent());
        $this->journey = $this->manager->getBuiltJourney($this->cardsArray);

        // Saving the journey to DB and getting the journey with the INSERT id
        $this->journey = $this->manager->persistJourney($this->journey);
    }
}
