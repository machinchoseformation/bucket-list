<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Wish;
use App\Form\CommentType;
use App\Form\WishType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends Controller
{
    /**
     * @Route("/idees/liste", name="wish_list")
     */
    public function list()
    {
        $repo = $this->getDoctrine()->getRepository(Wish::class);
        //voir dans WishRepository pour cette méthode perso
        $wishes = $repo->findListWishes();

        return $this->render("wish/list.html.twig", [
            "wishes" => $wishes,
        ]);
    }


    /**
     * @Route("/details/{id}", name="wish_detail")
     */
   public function detail(Wish $wish, Request $request)
   {
       $comment = new Comment();
       $commentForm = $this->createForm(CommentType::class, $comment);

       $commentForm->handleRequest($request);

       if ($commentForm->isSubmitted() && $commentForm->isValid()){
           $comment->setDateCreated( new \DateTime() );
           $comment->setWish($wish);

           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($comment);
           $entityManager->flush();

           $this->addFlash("success", "Votre commentaire a été publié !");
           //on redirige ici-même pour vider le formulaire et éviter la resoumission des données
           return $this->redirectToRoute("wish_detail", [
               "id" => $wish->getId()
           ]);
       }

       return $this->render("wish/detail.html.twig", [
           "wish" => $wish,
           "commentForm" => $commentForm->createView()
       ]);
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
     * @Route("/idee/modifier/{id}", name="wish_edit")
     */
    public function edit($id, Request $request)
    {
        //récupère le wish à modifier depuis la bdd
        $wishRepository = $this->getDoctrine()->getRepository(Wish::class);
        $wish = $wishRepository->find($id);

        if (empty($wish)){
            throw $this->createNotFoundException("Oups ! Cette idée n'existe pas !");
        }

        //crée le form en lui passant notre instance
        $form = $this->createForm(WishType::class, $wish);
        //prend les données soumises et les injecte dans notre entité
        $form->handleRequest($request);

        //si le formulaire est valide...
        if ($form->isSubmitted() && $form->isValid()){
            //renseigne les champs manquants
            $wish->setDateUpdated( new \DateTime() );

            //sauvegarde
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            //redirige avec un message de succès
            $this->addFlash("success", "Votre idée a bien été modifiée !");
            return $this->redirectToRoute('home');
        }

        return $this->render("wish/edit.html.twig", [
            "form" => $form->createView()
        ]);
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
