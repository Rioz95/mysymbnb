<?php

namespace App\Service;

use App\Service\UploadFichierInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadImageUpdateUser implements UploadFichierInterface
{
    private $param;
    public function __construct(ParameterBagInterface $param)
    {
        $this->param = $param;
    }

    /**
     * Uploide une image dans le dossier de destination et retoutne son nom ou null
     * 
     * @param UploadedFile $fichier  à uploid"
     * @param String $nomImageActuel"
     * @return String|null retourne le nouveau du l'image ou null
     * 
     */
    public function enregistreImage(UploadedFile $fichier, String $nomImageActuel)
    {
        // on recupère l'image selectioner
        // $fichierImage = $form->get('imageFile')->getData();
        $reperitoireDestination = $this->param->get("imagesUsersDestination");
        if ($fichier != null) {
            //on supprime l'ancien fichier
            /* if ($nomImageActuel != "user.jpg") {
                \unlink($reperitoireDestination . $nomImageActuel);
            } */
            //on créer un nouveaux fichier
            $nouveauNomichier = md5(\uniqid()) . "." . $fichier->guessExtension();
            //on deplace le fichier chargé dans le dossier public
            $fichier->move($reperitoireDestination, $nouveauNomichier);
            return $nouveauNomichier;
        } else {
            return null;
        }
    }
}
