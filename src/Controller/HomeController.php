<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $indexBooks = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findAll();
            // ->findBy(['isReturned' => true],['dateEnd' => 'ASC'] ,5);

        return $this->render('home/index.html.twig', [
            'indexBooks' => $indexBooks,
        ]);
    }
}
