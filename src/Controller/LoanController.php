<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\User;
use App\Form\LoanType;
use App\Repository\LoanRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LoanController extends AbstractController
{
    /**
     * @Route("/livres-pretes/{id}", name="loan_index_admin", methods={"GET"})
     */
    public function indexAdminLoan(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/index.html.twig', [
            'loans' => $loanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/livres-empruntes/{id}", name="loan_index_user", methods={"GET"})
     */
    public function indexUserLoan(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/indexUser.html.twig', [
            'loans' => $loanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{idBook}/user/{idUser}", name="loan_new", methods={"GET","POST"})
     * @ParamConverter("book", class="App\Entity\Book", options={"mapping": {"idBook": "id"}})
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"idUser": "id"}})
     */
    public function new(Request $request, Book $book, User $user): Response
    {
        if (in_array('ROLE_USER', $user->getRoles(), true)) {

            $entityManager = $this->getDoctrine()->getManager();
            $loan = new Loan();

            $dateStart = new \DateTime('now');
            $dateEnd = new \DateTime('now');
            $dateEnd = $dateEnd->modify('+1 month') ;
            $loan->setDateStart($dateStart, ['format' => 'yyyy-MM-dd']);
                    $loan->setDateEnd($dateEnd , ['format' => 'yyyy-MM-dd']);

            $loan->setIsReturned(false);
            $loan->setBook($book);

            $loan->setUser($user);

            $entityManager->persist($loan);
            $entityManager->flush();

            $this->addFlash('success', 'Vous venez d\'emprunter le livre '. $book->getTitle() .' pour 1 mois');
            return $this->redirectToRoute('loan_index_user', ['id' => $user->getId()]);

        }
    }

    /**
     * @Route("/{id}", name="loan_show", methods={"GET"})
     */
    // public function show(Loan $loan): Response
    // {
    //     return $this->render('loan/show.html.twig', [
    //         'loan' => $loan,
    //     ]);
    // }

    /**
     * @Route("/{id}/edit", name="loan_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Loan $loan): Response
    {
        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('loan_index');
        }

        return $this->render('loan/edit.html.twig', [
            'loan' => $loan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="loan_delete", methods={"POST"})
     */
    public function delete(Request $request, Loan $loan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$loan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($loan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('loan_index_user', ['id' => $loan->getUser()->getId()]);
    }
}
