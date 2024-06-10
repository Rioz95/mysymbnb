<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/admin/comment/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(CommentRepository $comments, $page, PaginationService $pagination): Response
    {
        $pagination->setEnityClass(Comment::class)
            ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }


    /**
     * Permet d'afficher et modifier un commentaire
     * @Route("/admin/comment/{id}/edit", name="admin_comment_edit")
     * 
     */
    public function edit($id, EntityManagerInterface $manager, Request $request)
    {
        $comment = $this->manager->getRepository(Comment::class)->findOneById($id);

        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire <strong>{$comment->getId()}</strong> a été bien modifier !"
            );
            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de supprimer un commentaire
     * 
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     */
    public function delete(Comment $comment, EntityManagerInterface $manager)
    {

        $manager->remove($comment);
        $manager->flush();
        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> a été bien supprimer !"
        );
        return $this->redirectToRoute('admin_comment_index');
    }
}
