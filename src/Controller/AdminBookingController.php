<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination): Response
    {
        $pagination->setEnityClass(Booking::class)
            ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Permet d'afficher et modifier un reservations
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     * 
     */

    public function edit($id, EntityManagerInterface $manager, Request $request)
    {
        $booking = $this->manager->getRepository(Booking::class)->findOneById($id);

        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation <strong>{$booking->getId()}</strong> a été bien modifier !"
            );
            return $this->redirectToRoute('admin_booking_index');
        }


        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de supprimer une réservation
     * 
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     * 
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * 
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {
        $manager->remove($booking);
        $manager->flush();
        $this->addFlash(
            'success',
            "La réservations de <strong>{$booking->getBooker()->getFullName()}</strong> a été bien supprimer !"
        );
        return $this->redirectToRoute('admin_booking_index');
    }
}
