<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Pif\Pouf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $wishRepository = $this->getDoctrine()->getRepository(Wish::class);

        //retourne la totalité des données de la table
        //$wishes = $wishRepository->findAll();

        $wishes = $wishRepository->findBy(
            [], //clauses where
            ["dateCreated" => "DESC", "label" => "ASC"], //order by
            5, //limit
            0 //offset
        );

        //compte le nombre total d'idées présentes en bdd
        $wishesCount = $wishRepository->count([]);

        //voir dans le dossier /templates
        return $this->render("main/home.html.twig", [
            "wishes" => $wishes,
            "wishesCount" => $wishesCount,
        ]);
    }

    /**
     * @Route("/foire-aux-questions", name="faq")
     */
    public function faq()
    {
        return $this->render("main/faq.html.twig");
    }


    /**
     * @Route("/cgu", name="cgu")
     */
    public function cgu()
    {
        return $this->render('main/cgu.html.twig');
    }


    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render("main/contact.html.twig");
    }

    /**
     * @Route("/test/{page}",
     *     name="test",
     *     requirements={"id": "^\d+$"})
     */
    public function test($page = 1, Request $request)
    {
        //dump($request);
        return $this->render("main/test.html.twig", ["id" => $page]);
    }

}