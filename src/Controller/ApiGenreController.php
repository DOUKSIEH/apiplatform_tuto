<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiGenreController extends AbstractController
{
    /**
     * @Route("/api/genres", name="api_genres" , methods={"GET"})
     */
    public function list(GenreRepository $repo, SerializerInterface $serialiser)
    {
        $genre = $repo->findAll();
        $resultat = $serialiser->serialize(
            $genre,
            'json',
            [
                "groups" => ['listGenrefull']
            ]
        );
      
        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse($resultat,200,[],true);
    }
    /**
     * @Route("/api/genres/{id}", name="api_genres_show" , methods={"GET"})
     */
    public function show(Genre $genre, SerializerInterface $serialiser)
    {
       // $genre = $repo->findAll();
        $resultat = $serialiser->serialize(
            $genre,
            'json',
            [
                "groups" => ['listGenreSimple']
            ]
        );

        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }
    /**
     * @Route("/api/genres", name="api_genres_create" , methods={"POST"})
     */
    public function create (Request $request, SerializerInterface $serialiser, ObjectManager $manager, ValidatorInterface $validator)
    {
        // $genre = $repo->findAll();

        $data = $request->getContent();

      // $serialiser->deserialize($data,Genre::class,'json',['Machin' => $genre]);
       
       $genre = $serialiser->deserialize($data, Genre::class, 'json');

        // gestion des erreurs de validation
        $errors = $validator->validate($genre);

        if (count($errors)) {

            $errorsJson = $serialiser->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);

        }
       $manager->persist($genre);
       $manager->flush();
       // $resultat = $serialiser->serialize(null,Response::HTTP_CREATED,["groups" => ['listGenrefull']]);

        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse(

            "Le genre a bien été créé",

            Response::HTTP_CREATED, 
           // [     "location" => "/api/genres/".$genre->getId()        ], 
           [
            "Location" => $this->generateUrl(
                'api_genres_show',
                ["id" => $genre->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL)
           ]
           ,
            true
        );
    }
    /**
     * @Route("/api/genres/{id}", name="api_genres_update" , methods={"PUT"})
     */
    public function edit (Genre $genre, SerializerInterface $serialiser, ObjectManager $manager, Request $request, ValidatorInterface $validator)
    {
        // $genre = $repo->findAll();
        $data = $request->getContent();
         $serialiser->deserialize($data, Genre::class, 'json',[
             'object_to_populate' => $genre
         ]);
        // gestion des erreurs de validation
        $errors = $validator->validate($genre);
        if (count($errors)) {
            $errorsJson = $serialiser->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        } 
        $manager->persist($genre);
        $manager->flush();
        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse('Le genre a bien été modifié', Response::HTTP_OK, [], true);
    }
     /**
     * @Route("/api/genres/{id}", name="api_genres_delete" , methods={"DELETE"})
     */
    public function delete (Genre $genre, ObjectManager $manager)
    {
            
        $manager->remove($genre);
        $manager->flush();
        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse('Le genre a bien été supprimé', Response::HTTP_OK, []);
    }
    

}
