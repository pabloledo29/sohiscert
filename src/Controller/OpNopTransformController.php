<?php

namespace App\Controller;

use App\Entity\OpNopTransform;
use App\Form\OpNopTransformType;
use App\Repository\OpNopTransformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/listar_nop")
 */
class OpNopTransformController extends AbstractController
{
    /**
     * @Route("/", name="nop_transform_index", methods={"GET"})
     */
    public function index(OpNopTransformRepository $OpNopTransformRepository): Response
    {
        return $this->render('nop_transform/index.html.twig', [
            'nop_transforms' => $OpNopTransformRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nuevo", name="nop_transform_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $nopTransform = new OpNopTransform();
    

        $form = $this->createForm(OpNopTransformType::class, $nopTransform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nopTransform);
            $entityManager->flush();

            return $this->redirectToRoute('nop_transform_index');
        }

        return $this->render('nop_transform/new.html.twig', [
            'nop_transform' => $nopTransform,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="nop_transform_show", methods={"GET"})
     */
    public function show(OpNopTransform $nopTransform): Response
    {
        return $this->render('nop_transform/show.html.twig', [
            'nop_transform' => $nopTransform,
        ]);
    }

    /**
     * @Route("/{id}/editar", name="nop_transform_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, OpNopTransform $nopTransform): Response
    {
        $form = $this->createForm(OpNopTransformType::class, $nopTransform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nop_transform_index');
        }

        return $this->render('nop_transform/edit.html.twig', [
            'nop_transform' => $nopTransform,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="nop_transform_delete", methods={"DELETE"})
     */
    public function delete(Request $request, OpNopTransform $nopTransform): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nopTransform->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nopTransform);
            $entityManager->flush();
        }

        return $this->redirectToRoute('nop_transform_index');
    }
}
