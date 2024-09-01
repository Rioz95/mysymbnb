<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
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
            //On récupère les images transmise
            $images = $form->get('images')->getData();

            //ON boucle sur les images
            foreach ($images as $image) {
                // On génère un noveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                //On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('imagesAnnonceDestination'),
                    $fichier
                );

                //On stock l'image dans la base de donner
                $img = new Image();
                $img->setCaption($fichier);
                $ad->addImage($img);
            }

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

    /**
     * Pemret de supprimer une image
     * @Route("/delete-image/{id}", name="delete_image", methods={"DELETE"})
     */
    public function deleteImage($id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $image = $entityManager->getRepository(Image::class)->find($id);

        if (!$image) {
            return new Response(json_encode(['success' => false, 'message' => 'Image non trouvée.']), 404, ['Content-Type' => 'application/json']);
        }

        // Vérifiez le token CSRF
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete' . $id, $token)) {
            return new Response(json_encode(['success' => false, 'message' => 'Jeton CSRF invalide.']), 400, ['Content-Type' => 'application/json']);
        }

        // Supprimez l'image
        $entityManager->remove($image);
        $entityManager->flush();

        return new Response(json_encode(['success' => true]), 200, ['Content-Type' => 'application/json']);
    }
}
