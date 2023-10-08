<?php 

namespace App\Controller;


use App\Entity\Picture;
use App\Entity\User;
use App\Services\UploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\UX\Cropperjs\Factory\CropperInterface;
use Symfony\UX\Cropperjs\Form\CropperType; 



#[Route('profil/{id<\d+>}/cropper')]
class CropperController extends AbstractController
{

    const DIRECTORY_PICTURE = '/uploads/pictures/';
    
    public function __construct(private ManagerRegistry $doctrine, private Security $security, private Packages $assets) 
    {
        $this->projectDir = 'https://'. $_SERVER['SERVER_NAME'] . self::DIRECTORY_PICTURE;
        $this->serverName = $_SERVER['SERVER_NAME'];
    }
    private $projectDir;
    private $serverName;
    
    public function getUrlPicture() {
        return 'http://'. $this->serverName . self::DIRECTORY_PICTURE;
    }
    
    

    #[Route('/{pictureSend}', name: 'app_cropper_util')]
    public function cropperUtil(User $user = null, Picture $picture = null, CropperInterface $cropper, Packages $package, Request $request, UploaderService $uploaderService, string $pictureSend/*, string $projectDir*/): Response
    {
        
        $picture = new Picture();
        
        $crop = $cropper->createCrop($this->projectDir . $pictureSend);
        
        $crop->setCroppedMaxSize(1000, 750);

        $form = $this->createFormBuilder(['crop' => $crop])
            ->add('crop', CropperType::class, [
                'public_url' =>  $package->getUrl($this->projectDir . $pictureSend),
                'cropper_options' => [
                    //'aspectRatio' => 4 / 3,
                    'aspectRatio' => 200 / 200,
                    'preview' => '#cropper-preview',
                    'scalable' => false,
                    'zoomable' => false,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // faking an error to let the page re-render with the cropped images
            $form->addError(new FormError('🤩'));
            
            $croppedImage = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedImage()));
            //$croppedThumbnail = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedThumbnail(200, 150)));
            list($dataType, $imageData) = explode(';', $croppedImage);
            // image file extension
            $imageExtension = explode('/', $dataType)[1];
            // base64-encoded image data
            list(, $encodedImageData) = explode(',', $imageData);
            // decode base64-encoded image data
            $decodedImageData = base64_decode($encodedImageData);
           
            $byte = random_bytes(4);
            $final = bin2hex($byte);
           
            file_put_contents("uploads/pictures/profil_{$final}_{$pictureSend}", $decodedImageData);
                        
            $manager = $this->doctrine->getManager();
            
            $picture->setStatus('1');
            $picture->setFilename("profil_{$final}_{$pictureSend}");
            $picture->setLegend('crop');
            $picture->setAlt('crop');
            $picture->setProfil('1');
            $user->addPicture($picture);
            $user->setPictureProfil($picture);
            //$user->setPictureProfil(null);    //pour enlever foto de profil et delete user 
            $manager->persist($picture);
            $manager->persist($user);   //enregistre picture associé a un user 
            $manager->flush();

            $this->addFlash('success', "Votre image de profil à bien eté mise a jour");
            return $this->redirectToRoute('info',['id' => $user->getId()]);  
        }

        /*if ($form->isSubmitted() && $form->isValid()) {
          
            $crop->getCroppedImage();

            $crop->getCroppedThumbnail(200, 150);

        
        }*/
        $auth = $this->security->getUser();
        //return $this->redirectToRoute('info',['id' => $user->getId()]);  
        return $this->render('user/profil/cropper/index.html.twig', [
            'classLeftMenuProfiSelected' => '1',
            'classSelectedInfo' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'package' => $package,
            'form' => $form,
            'croppedImage' => isset($croppedImage) ? $croppedImage : null,
            'croppedThumbnail' => isset($croppedThumbnail) ? $croppedThumbnail : null
        ]);
    
}

}