<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

        return $this->render('home/home.html.twig', [
            'indexBooks' => $indexBooks,
        ]);
    }

    /**
     * @Route("/livres", name="index_books")
     */
    public function indexBooks(): Response
    {
        $indexBooks = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findAll();
            // ->findBy(['isReturned' => true],['dateEnd' => 'ASC'] ,5);

        return $this->render('home/indexBooks.html.twig', [
            'indexBooks' => $indexBooks,
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Book $book): Response
    {
        return $this->render('home/showBook.html.twig', [
            'book' => $book,
        ]);
    }
}
