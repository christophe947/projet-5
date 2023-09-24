<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\UX\Cropperjs\Factory\CropperInterface;
use Symfony\UX\Cropperjs\Form\CropperType; 

// ...


class CropperController extends AbstractController
{
    #[Route('profil/{id<\d+>}/cropper', name: 'app_cropp_test')]
    public function cropper(CropperInterface $cropper, Request $request): Response
    {
        $crop = $cropper->createCrop('../../assets/images/blue.jpg');
        $crop->setCroppedMaxSize(2000, 1500);

        $form = $this->createFormBuilder(['crop' => $crop])
            ->add('crop', CropperType::class, [
                'public_url' => '../../assets/images/blue.jpg',
                'cropper_options' => [
                    'aspectRatio' => 2000 / 1500,
                ],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the cropped image data (as a string)
            $crop->getCroppedImage();

            // Create a thumbnail of the cropped image (as a string)
            $crop->getCroppedThumbnail(200, 150);

            // ...
        }

        return $this->render('user/profil/cropper/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    

    #[Route('profil/{id<\d+>}/cropperjs', name: 'app_cropper_util')]
    public function cropperUtil(CropperInterface $cropper, Packages $package, Request $request/*, string $projectDir*/): Response
    {
        $crop = $cropper->createCrop(/*$projectDir.*/'../../assets/images/blue.jpg');
        $crop->setCroppedMaxSize(1000, 750);

        $form = $this->createFormBuilder(['crop' => $crop])
            ->add('crop', CropperType::class, [
                'public_url' => $package->getUrl('../assets/images/blue.jpg'),
                'cropper_options' => [
                    'aspectRatio' => 4 / 3,
                    'preview' => '#cropper-preview',
                    'scalable' => false,
                    'zoomable' => false,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        $croppedImage = null;
        $croppedThumbnail = null;
        if ($form->isSubmitted()) {
            // faking an error to let the page re-render with the cropped images
            $form->addError(new FormError('🤩'));
            $croppedImage = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedImage()));
            $croppedThumbnail = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedThumbnail(200, 150)));
        }

        return $this->render('user/profil/cropper/cropper.html.twig', [
            'form' => $form,
            'croppedImage' => $croppedImage,
            'croppedThumbnail' => $croppedThumbnail,
        ]);
    
}

}