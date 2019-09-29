<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/listeRegions", name="listeRegions")
     */
    public function listeRegions(SerializerInterface $serializer)
    {
        $regions = file_get_contents('https://geo.api.gouv.fr/regions');
        //$this->json('https://geo.api.gouv.fr/regions');
        //file_get_contents('https://geo.api.gouv.fr/regions');
       $regionstab = $serializer->decode($regions,'json');

      $regionsobj = $serializer->denormalize($regionstab, 'App\Entity\Region[]');
        $regions = $serializer->deserialize($regions, 'App\Entity\Region[]','json');
   
       dump($regionsobj);
        //die();
        return $this->render('api/index.html.twig', [
            'controller_name' => 'Liste des régions',
            'regions' => $regions
        ]);
    }
    /**
     * @Route("/listedpsRegions", name="listedepartRegions")
     */
    public function listeDpsParRe(SerializerInterface $serializer,Request $request)
    {
      // je recupère la région sélectionnée dans le formulaire

      $codeRegion = $request->query->get('region');

      //je recupère la liste  des régions

        $regions = file_get_contents('https://geo.api.gouv.fr/regions');
    
        $regions = $serializer->deserialize($regions, 'App\Entity\Region[]', 'json');

        //Permet de recuperer la liste des départements

        if ($codeRegion == null || $codeRegion == 'Toutes')
        {
            $deps =  file_get_contents('https://geo.api.gouv.fr/departements');
        }
        else 
        {
            $deps =  file_get_contents('https://geo.api.gouv.fr/regions/'.$codeRegion.'/departements');
            # code...
        }

        // Permet de tranformer de json en tableau

        $deps = $serializer->decode($deps,'json');

        //die();
        return $this->render('api/listdepsRegion.html.twig', [
            'controller_name' => 'Liste des départements par région',
            'regions' => $regions,
            'deps'  => $deps
        ]);
    }
}

