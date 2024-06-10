<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Service\UploadImage;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use App\Service\UploadFichierInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/ads/{page<\d+>?1}", name="ads_index")
     */
    public function index(AdRepository $repo, PaginationService $pagination, $page): Response
    {
        //$ads = $repo->findAll();
        $pagination->setEnityClass(Ad::class)
            ->setLimit(6)
            ->setPage($page);

        return $this->render('ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @route("/ads/new", name="ads_create")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager, UploadImage $uploader)
    {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $nouvauNomImage = $uploader->enregistreImage($form->get('imageFile')->getData(), $ad->getCoverImage());

            if ($nouvauNomImage != null) {
                $ad->setCoverImage($nouvauNomImage);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a été bien enregisttée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug(),
            ]);
        }


        return $this->render('ad/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher le formulaiure d'édition
     * 
     * @route("ads/{slug}/edit", name="ads_edit")
     *@Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * 
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Ad $ad, UploadImage $uploader)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
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

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug(),
            ]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
        ]);
    }

    /**
     * Permet d'afficheruneseule annonce
     * 
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show($slug, Ad $ad)
    {
        //je recupère l'annonce qui correspond au slug
        //$ad = $repo->findOneBySlug($slug);
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }
    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous n'avez le droit d'acceder à cette ressource")
     *  
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a été bien supprimer !"
        );

        return $this->redirectToRoute('ads_index');
    }
}
