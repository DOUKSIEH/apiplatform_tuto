<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiAuteurController extends AbstractController
{
   
    /**
     * @Route("/api/auteurs", name="api_auteurs" , methods={"GET"})
     */
    public function list(AuteurRepository $repo, SerializerInterface $serialiser)
    {
    
        $auteur = $repo->findAll();
        $resultat = $serialiser->serialize(
            $auteur,
            'json',
            [
                "groups" => ['listAuteurfull']
            ]
        );

        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse($resultat, 200, [], true);
    }
    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_show" , methods={"GET"})
     */
    public function show(Auteur $auteur, SerializerInterface $serialiser)
    {
        // $auteur = $repo->findAll();
        $resultat = $serialiser->serialize(
            $auteur,
            'json',
            [
                "groups" => ['listAuteurSimple']
            ]
        );

        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }
    /**
     * @Route("/api/auteurs", name="api_auteurs_create" , methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serialiser, ObjectManager $manager, ValidatorInterface $validator)
    {
        // $auteur = $repo->findAll();

        $data = $request->getContent();

        // $serialiser->deserialize($data,Auteur::class,'json',['Machin' => $auteur]);

        $auteur = $serialiser->deserialize($data, Auteur::class, 'json');

        // gestion des erreurs de validation
        $errors = $validator->validate($auteur);

        if (count($errors)) {

            $errorsJson = $serialiser->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($auteur);
        $manager->flush();
        // $resultat = $serialiser->serialize(null,Response::HTTP_CREATED,["groups" => ['listAuteurfull']]);

        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse(

            "L'auteur a bien été créé",

            Response::HTTP_CREATED,
            // [     "location" => "/api/auteurs/".$auteur->getId()        ], 
            [
                "Location" => $this->generateUrl(
                    'api_auteurs_show',
                    ["id" => $auteur->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ],
            true
        );
    }
    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_update" , methods={"PUT"})
     */
    public function edit(Auteur $auteur, SerializerInterface $serialiser, ObjectManager $manager, Request $request, ValidatorInterface $validator)
    {
        // $auteur = $repo->findAll();
        $data = $request->getContent();
        $serialiser->deserialize($data, Auteur::class, 'json', [
            'object_to_populate' => $auteur
        ]);
        // gestion des erreurs de validation
        $errors = $validator->validate($auteur);
        if (count($errors)) {
            $errorsJson = $serialiser->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($auteur);
        $manager->flush();
        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse('L\'auteur a bien été modifié', Response::HTTP_OK, [], true);
    }
    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_delete" , methods={"DELETE"})
     */
    public function delete(Auteur $auteur, ObjectManager $manager)
    {

        $manager->remove($auteur);
        $manager->flush();
        //dd(new JsonResponse($resultat, 200, [], true));

        return new JsonResponse('L\'auteur a bien été supprimé', Response::HTTP_OK, []);
    }
}
