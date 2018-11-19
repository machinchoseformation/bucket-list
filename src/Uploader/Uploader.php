<?php

namespace App\Uploader;

use claviska\SimpleImage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    private $uploadDir = "";
    private $smallUploadDir = "";

    /** @var UploadedFile */
    private $uploadedFile;

    private $extension = "";
    private $newFileName = "";
    private $newFileNameWithExt = "";

    public function __construct(ContainerInterface $container)
    {
        $this->uploadDir = $container->getParameter("upload_dir") . "/";
        $this->smallUploadDir = $container->getParameter("upload_dir") . "/small/";
        try {
            $this->newFileName = bin2hex(random_bytes(12));
        } catch(\Exception $e){
            die($e->getMessage());
        }
    }

    public function setUploadedFile(?UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        if ($this->uploadedFile) {
            $error = $this->hasErrors();

            if (!$error) {
                //récupère l'extension réelle du fichier. Peut retourner null
                $this->extension = $this->uploadedFile->guessExtension();
                //si pas d'erreur d'upload...
                if (empty($this->extension)) {
                    return new FormError('Fichier invalide. Pas d\'extension');
                }
                $this->newFileNameWithExt = $this->newFileName . "." . $this->extension;
            }
            else {
                return $error;
            }
        }
    }

    public function hasErrors()
    {
        if ($this->uploadedFile) {
            if ($this->uploadedFile->getError() > 0){
                return new FormError('Fichier invalide. Code ' . $this->uploadedFile->getError());
            }
        }

        return false;
    }

    public function uploadFile()
    {
        if ($this->uploadedFile) {
            $ext = $this->uploadedFile->guessExtension();

            //php7 requis pour random_bytes()
            try{
                $newName = bin2hex(random_bytes(12));
            } catch (\Exception $e){
                die($e->getMessage());
            }

            //déplace le fichier temporaire vers notre dossier
            $this->uploadedFile->move($this->uploadDir, $this->newFileNameWithExt);

            try {
                $simpleImage = new SimpleImage();
                $filePath = $this->uploadDir . $this->newFileNameWithExt;
                $simpleImage->fromFile($filePath)
                    ->bestFit(300, 300)
                    ->desaturate()
                    ->toFile($this->smallUploadDir . $this->newFileNameWithExt);
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }
    }

    /**
     * @return string
     */
    public function getNewFileNameWithExt(): string
    {
        return $this->newFileNameWithExt;
    }
}