<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

Class UploaderService 
{
    public function __construct(private  SluggerInterface $slugger) {
        
    }
    public function uploadFile(UploadedFile $file, string $directoryFolder) {
        
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
       
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        
        try {
            $file->move(
                $directoryFolder,
                $newFilename
            );
        } catch (FileException $e) {
            
        // ... handle exception if something happens during file upload
        }
        return $newFilename;
    }
    
}