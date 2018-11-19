<?php

namespace App\Controller;

use App\Entity\UserWish;
use App\Entity\Wish;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class ListController extends Controller
{
    /**
     * @Route("/perso", name="list_list")
     */
    public function list()
    {
        $userWishRepository = $this->getDoctrine()->getRepository(UserWish::class);
        $userWishes = $userWishRepository->findUserList($this->getUser());
        return $this->render('list/list.html.twig', [
            'userWishes' => $userWishes
        ]);
    }

    /**
     * @Route("/perso/ajout/{id}", name="list_add")
     */
    public function add(Wish $wish)
    {
        $userWish = new UserWish();
        $userWish->setWish($wish);
        $userWish->setUser($this->getUser());
        $userWish->setDone(false);
        $userWish->setDateAdded(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($userWish);
        $em->flush();

        $this->addFlash('success', "L'idée a bien été ajoutée à votre liste !");
        return $this->redirectToRoute('list_list');
    }

    /**
     * @Route("/perso/suppression/{id}", name="list_remove")
     */
    public function remove(UserWish $userWish)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($userWish);
        $em->flush();

        $this->addFlash('success', "L'idée a bien été retirée de votre liste !");
        return $this->redirectToRoute('list_list');
    }

    /**
     * @Route("/perso/realisation/{id}", name="list_toggle_done")
     */
    public function toggleDone(UserWish $userWish)
    {
        $userWish->setDone( !$userWish->getDone() );
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('list_list');
    }
}