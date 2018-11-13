<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends Controller
{
    /**
     * @Route("/details/{id}", name="wish_detail")
     */
   public function detail(Wish $wish)
   {
       return $this->render("wish/detail.html.twig", ["wish" => $wish]);
   }

   /**
    * @Route("/idee/supprimer/{id}", name="wish_remove")
    */
   public function remove(Wish $wish)
   {
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->remove($wish);
       $entityManager->flush();

       $this->addFlash("success", "L'idée a bien été supprimée !");

       return $this->redirectToRoute("home");
   }


   /**
    * @Route("/idee/creer", name="wish_create")
    */
   public function create(Request $request)
   {
       //une instance de notre entité qu'on associe au form
       $wish = new Wish();

       //crée le form en lui passant notre instance
       $form = $this->createForm(WishType::class, $wish);
        //prend les données soumises et les injecte dans notre entité
       $form->handleRequest($request);

       //si le formulaire est valide...
       if ($form->isSubmitted() && $form->isValid()){
            //renseigne les champs manquants
           $wish->setDateCreated( new \DateTime() );

           //sauvegarde
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($wish);
           $entityManager->flush();

           //redirige avec un message de succès
           $this->addFlash("success", "Votre idée a bien été ajoutée !");
           return $this->redirectToRoute('home');
       }

       return $this->render("wish/create.html.twig", [
           "form" => $form->createView()
       ]);
   }



}
