<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadFichierInterface
{
    /**
     * Uploide une image dans le dossier de destination et retoutne son nom ou null
     * 
     * @param UploadedFile $fichier  à uploid"
     * @param String $nomImageActuel"
     * @return String|null retourne le nouveau du l'image ou null
     * 
     */
    public function enregistreImage(UploadedFile $fichier, String $nomImageActuel);
}
