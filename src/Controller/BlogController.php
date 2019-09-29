<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
Use  Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Form\ArticleType;
use App\Entity\Article;
use App\Repository\ArticleRepository;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
       // $repo = $this->getDoctrine()->getRepository(Article::class);
        
        $article = $repo->findAll();
      
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=> $article
             ]);
    }
     /**
     * @Route("/", name="home")
     */
    public function home(){
      return $this->render('blog/home.html.twig',[
       'title'=> "Bienvenue",
        'age'=> 31
        ]);
    }
     /**
      * @Route("blog/new",name="blog_create")
      * @Route("/blog/{id}/edit", name="blog_edit")
      */
    public  function form(Article $article = null,Request $request, ObjectManager $manager ){
      dump($request);

//    $article = new Article();

//    $article ->setTitle("Titre d'exemple")
  //           ->setContent("Le content de l'article");
   if(!$article){
    $article = new Article();
   }

    /*$form = $this->createFormBuilder($article)
        ->add('title')
        ->add('content')
        ->add('image')
       // ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ->getForm();*/
     $form = $this->createForm(ArticleType::class,$article);   

        $form->handleRequest($request);

       // dump($article);
       if ($form->isSubmitted() && $form->isValid()) {
         if(!$article->getId()){
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        $article->setCreatedAt(new \DateTime()); 
      }
        //$task = $form->getData();

        // ... perform some action, such as saving the task to the database
        // for example, if Task is a Doctrine entity, save it!
        // $entityManager = $this->getDoctrine()->getManager();
          $manager->persist($article);
          $manager->flush();

        return $this->redirectToRoute('blog_show',['id'=> $article->getId()]);
    }

    
     /* if($request->request->count()>0){

        $article = new Article();
        $article->setTitle($request->request->get('title'))
                ->setContent($request->request->get('content'))
                ->setImage($request->request->get('image'))
                ->setCreatedAt(new \Datetime());

      $manager->persist($article);
      $manager->flush();
       return $this->redirectToRoute('blog_show',['id'=> $article->getId()]);   
                  
    }*/
      return $this->render('blog/create.html.twig',[
        'formArticle'=> $form->createView(),
        'editMode' => $article->getId()!==null
      ]);
    }
      /**
     * @Route("/blog/{id}", name="blog_show")
     */
     public function show(Article $article){
      
    //  $repo = $this->getDoctrine()->getRepository(Article::class);
        
     // $article = $repo->find($id);
        
         return $this->render('blog/show.html.twig',[
           'article'=> $article
         ]);
     }
     
}
