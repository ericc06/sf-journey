<?php

namespace App\Controller;

//require_once __DIR__.'/../vendor/autoload.php';

use App\Entity\Card;
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
     * @Route("/journey", name="journey", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        $this->cardsArray = $this->manager->getCardsArrayFromJson($request->getContent());

        $this->journey = $this->manager->buildJourney($this->cardsArray);

        $jsonContent = $this->manager->getSerializedJourney($this->journey);

        $response = JsonResponse::fromJsonString($jsonContent);

        return $response;
    }

    /**
     * @Route("/journey-text", name="journey-text", methods={"GET", "POST"})
     */
    public function indexTest(Request $request): Response
    {
        $this->cardsArray = $this->manager->getCardsArrayFromJson($request->getContent());

        $this->journey = $this->manager->buildJourney($this->cardsArray);

        //$jsonContent = $this->manager->getSerializedJourney($this->journey);
        $textContent = $this->manager->getTextualJourney($this->journey);

        $response = new Response($textContent);

        return $response;
    }

    /**
     * @Route("/journey2", name="journey2", methods={"GET", "POST"})
     */
    public function index2(Request $request): Response
    {
        //var_dump($trip); exit;
        $card0 = new Card();
        $card0->setStartLocation('Beauvais');
        $card0->setEndLocation('Strasbourg');
        $card0->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '18-Feb-2021 20:00:00'));
        $card0->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '18-Feb-2021 20:00:00'));
        $card0->setSeatNumber('W7-P64');
        $card0->setMeansType('TGV');
        $card0->setMeansNumber('AF123');

        $card1 = new Card();
        $card1->setStartLocation('Nice');
        $card1->setEndLocation('Paris');
        $card1->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card1->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card1->setSeatNumber('G6');
        $card1->setMeansType('plane');
        $card1->setMeansNumber('AF123');

        $card2 = new Card();
        $card2->setStartLocation('Paris');
        $card2->setEndLocation('Beauvais');
        $card2->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Feb-2021 20:00:00'));
        $card2->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Feb-2021 20:00:00'));
        $card2->setMeansType('bus');
        $card2->setMeansNumber('FL444');

        $card3 = new Card();
        $card3->setStartLocation('Rome');
        $card3->setEndLocation('Tunis');
        $card3->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card3->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card3->setSeatNumber('H3');
        $card3->setMeansType('plane');
        $card3->setMeansNumber('IT555');

        $card4 = new Card();
        $card4->setStartLocation('Milan');
        $card4->setEndLocation('Rome');
        $card4->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '15-Feb-2021 20:00:00'));
        $card4->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '15-Feb-2021 20:00:00'));
        $card4->setMeansType('bus');
        $card4->setMeansNumber('BU789');

        $card5 = new Card();
        $card5->setStartLocation('Nice');
        $card5->setEndLocation('Paris');
        $card5->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Mar-2021 20:00:00'));
        $card5->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Mar-2021 20:00:00'));
        $card5->setSeatNumber('G6');
        $card5->setMeansType('plane');
        $card5->setMeansNumber('AF123');

        $card6 = new Card();
        $card6->setStartLocation('Paris');
        $card6->setEndLocation('Beauvais');
        $card6->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Mar-2021 20:00:00'));
        $card6->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Mar-2021 20:00:00'));
        $card6->setMeansType('bus');
        $card6->setMeansNumber('FL444');

        $this->cardsArray[] = $card0;
        $this->cardsArray[] = $card4;
        $this->cardsArray[] = $card2;
        $this->cardsArray[] = $card3;
        $this->cardsArray[] = $card1;
        $this->cardsArray[] = $card5;
        $this->cardsArray[] = $card6;

        $this->journey = $this->manager->buildJourney($this->cardsArray);

        $jsonContent = $this->manager->getSerializedJourney($this->journey);

        $response = new Response();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }
}
