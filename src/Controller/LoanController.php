<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\User;
use App\Form\LoanType;
use App\Repository\LoanRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LoanController extends AbstractController
{
    /**
     * @Route("/livres-pretes/{idAdmin}", name="loan_index_admin", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAdminLoan(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/indexAdmin.html.twig', [
            'loans' => $loanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/livres-empruntes/{userId}", name="loan_index_user", methods={"GET"})
     */
    public function indexUserLoan(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/indexUser.html.twig', [
            'loans' => $loanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/livres-empruntes/new/{idBook}/user/{idUser}", name="loan_new", methods={"GET","POST"})
     * @ParamConverter("book", class="App\Entity\Book", options={"mapping": {"idBook": "id"}})
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"idUser": "id"}})
     */
    public function newBorrow(Request $request, Book $book, User $user): Response
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
            return $this->redirectToRoute('loan_index_user', ['userId' => $user->getId()]);

        }
    }

    /**
     * @Route("/livres-pretes/{idAdmin}/new", name="loan_new_admin", methods={"GET","POST"})
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"idAdmin": "id"}})
     */
    public function newLoan(Request $request, User $user): Response
    {
        $loan = new Loan();
        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($loan);
            $entityManager->flush();

            $this->addFlash('success', 'Un nouveau prêt a bien été crée');
            return $this->redirectToRoute('loan_index_admin', ['idAdmin' => $user->getId()]);
        }

        return $this->render('loan/new.html.twig', [
            'loan' => $loan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/livres-pretes/pret/{idLoan}", name="loan_show", methods={"GET"})
     * @ParamConverter("loan", class="App\Entity\Loan", options={"mapping": {"idLoan": "id"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Loan $loan): Response
    {
        return $this->render('loan/show.html.twig', [
            'loan' => $loan,
        ]);
    }

    /**
     * @Route("/{idLoan}/edit", name="loan_edit", methods={"GET","POST"})
     * @ParamConverter("loan", class="App\Entity\Loan", options={"mapping": {"idLoan": "id"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Loan $loan): Response
    {
        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Ce prêt a bien été édité');
            return $this->redirectToRoute('loan_show', ['idLoan' => $loan->getId()]);
        }

        return $this->render('loan/edit.html.twig', [
            'loan' => $loan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/livres-pretes/{idLoan}", name="loan_delete", methods={"POST"})
     * @ParamConverter("loan", class="App\Entity\Loan", options={"mapping": {"idLoan": "id"}})
     */
    public function delete(Request $request, Loan $loan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$loan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($loan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('loan_index_user', ['userId' => $loan->getUser()->getId()]);
    }
}
