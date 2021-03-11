<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProposalDetails;
use AppBundle\Form\ProposalDetailsType;
use AppBundle\Repository\ProposalDetailsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/proposal/details")
 */
class ProposalDetailsController extends Controller
{
    /**
     * @Route("/", name="proposal_details_index", methods={"GET"})
     */
    public function index(ProposalDetailsRepository $proposalDetailsRepository): Response
    {
        return $this->render('proposal_details/index.html.twig', [
            'proposal_details' => $proposalDetailsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="proposal_details_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $proposalDetail = new ProposalDetails();
        $form = $this->createForm(ProposalDetailsType::class, $proposalDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($proposalDetail);
            $entityManager->flush();

            return $this->redirectToRoute('proposal_details_index');
        }

        return $this->render('proposal_details/new.html.twig', [
            'proposal_detail' => $proposalDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="proposal_details_show", methods={"GET"})
     */
    public function show(ProposalDetails $proposalDetail): Response
    {
        return $this->render('proposal_details/show.html.twig', [
            'proposal_detail' => $proposalDetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="proposal_details_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProposalDetails $proposalDetail): Response
    {
        $form = $this->createForm(ProposalDetailsType::class, $proposalDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('proposal_details_index');
        }

        return $this->render('proposal_details/edit.html.twig', [
            'proposal_detail' => $proposalDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="proposal_details_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProposalDetails $proposalDetail): Response
    {
        if ($this->isCsrfTokenValid('delete'.$proposalDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($proposalDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('proposal_details_index');
    }
}
