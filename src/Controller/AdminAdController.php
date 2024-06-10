<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UploadImage;


class AdminAdController extends AbstractController
{

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination): Response
    {

        $pagination->setEnityClass(Ad::class)
            ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher et modifier une annonce
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @return Response
     */
    public function edit(Request $request, $id, UploadImage $uploader, EntityManagerInterface $manager)
    {
        $ad = $this->manager->getRepository(Ad::class)->findOneById($id);

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $nouvauNomImage = $uploader->enregistreImage($form->get('imageFile')->getData(), $ad->getCoverImage());

            if ($nouvauNomImage != null) {
                $ad->setCoverImage($nouvauNomImage);
            }


            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a été bien enregisttée !"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }
    /**
     * permet de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Impossible de supprimer l'annonce <strong>{$ad->getTitle()}</strong> , car elle contient déjà des réservations"
            );
        } else {

            $manager->remove($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a été bien supprimer !"
            );
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
