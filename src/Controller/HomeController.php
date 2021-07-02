<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Form\SearchFormType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $indexBooks = $bookRepository->findAvailableBooks();

        return $this->render('home/home.html.twig', [
            'indexBooks' => $indexBooks
        ]);
    }

    /**
     * @Route("/livres", name="index_books")
     */
    public function indexBooks(BookRepository $bookRepository): Response
    {
        $indexBooks = $bookRepository->findAvailableBooks();

        return $this->render('home/indexBooks.html.twig', [
            'indexBooks' => $indexBooks,
        ]);

    }

    /**
     * @Route("/livres/{idBook}", name="book_show", methods={"POST"})
     * @ParamConverter("book", class="App\Entity\Book", options={"mapping": {"idBook": "id"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Book $book): Response
    {
        return $this->render('home/showBook.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/livres/search", name="search", methods={"GET"})
     */
    public function search(Request $request, BookRepository $bookRepository): Response
    {
        $availableBooks = $bookRepository->findAvailableBooks();

        $search = $request->query->get('searchForm');
        $searchBooks = $bookRepository->searchBar($search);

        if (!$searchBooks) {
            $this->addFlash('danger', 'Aucun livre ne correspond à votre recherche');
        }

        return $this->render('home/search.html.twig', [
            'search' => $search,
            'searchBooks' => $searchBooks,
            'availableBooks' => $availableBooks,
        ]);
    }
}
