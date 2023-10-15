<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Picture;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Music;
use App\Form\PictureFormType;
use App\Form\UpdatePictureFormType;
use App\Form\UpdateVideoFormType;
use App\Form\UpdateMusicFormType;
use App\Form\VideoFormType;
use App\Form\AlbumFormType;
use App\Form\MusicFormType;
//use App\Services\ApiService;
//use App\Repository\AlbumRepository;
use App\Repository\MusicRepository;
use App\Repository\PictureRepository;
use App\Repository\VideoRepository;
use App\Services\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('profil/{id<\d+>}/media'), IsGranted('PUBLIC_ACCESS')]
class MediaController extends AbstractController
{
    const DIRECTORY_PICTURE = '/uploads/pictures/';
    //const DIRECTORY_PICTURE_PROFIL = '/uploads/profil_pictures/';
    const DIRECTORY_MUSIC = '/uploads/musics/';
    
    public function __construct(private Security $security, private ManagerRegistry $doctrine) {
        $this->serverName = $_SERVER['SERVER_NAME'];
    }
    private $serverName;
    
    public function getUrlPicture() {
        return 'https://'. $this->serverName . self::DIRECTORY_PICTURE;
    }

    /*public function getUrlPictureProfil() {
        return 'https://'. $this->serverName . self::DIRECTORY_PICTURE_PROFIL;
    }*/

    public function getUrlMusic() {
        return 'https://'. $this->serverName . self::DIRECTORY_MUSIC;
    }

    #[Route('/', name: 'media')]
    public function media(User $user = null): Response
    { 
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
          
        $auth = $this->security->getUser();

        return $this->render('user/profil/media/index.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

    #[Route('/albums', name: 'albums')]
    public function albums(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
    
        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albums = $albumRepository->findBy(['user' => $user->getId()], ['created_at' => 'DESC']);
       
        return $this->render('user/profil/media/album.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'albums' =>$albums
        ]);
    }

    /*#[Route('/albums/album-profil', name: 'album_render_profil')]
    public function albumProfil(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $urlPictureProfil = $this->getUrlPictureProfil();
        $auth = $this->security->getUser();
    
        $pictureRepository = $this->doctrine->getRepository(Picture::class);
        $albumPictureProfil = $pictureRepository->findBy(['user' => $user->getId()], ['created_at' => 'DESC', 'profil' => '1']);
       
        return $this->render('user/profil/media/album_render_profil.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'urlPictureProfil' => $urlPictureProfil,
            'user' => $user,
            'auth' => $auth,
            'albumPictureProfil' => $albumPictureProfil
        ]);
    }*/

    
    #[Route('/album/{albumId}', name: 'album_render')]
    public function albumRender(User $user = null, $albumId): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $urlPicture = $this->getUrlPicture();
        $urlMusic = $this->getUrlMusic();
    
        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albumRender = $albumRepository->findBy(['id' => $albumId]);
       
        $pictureRepository = $this->doctrine->getRepository(Picture::class);
        $albumPicture = $pictureRepository->findBy(['album' => $albumId], ['created_at' => 'DESC']);
        
        $videoRepository = $this->doctrine->getRepository(Video::class);
        $albumVideo = $videoRepository->findBy(['album' => $albumId], ['created_at' => 'DESC']);

        $musicRepository = $this->doctrine->getRepository(Music::class);
        $albumMusic = $musicRepository->findBy(['album' => $albumId], ['created_at' => 'DESC']);
        
        $auth = $this->security->getUser();

        return $this->render('user/profil/media/album_render.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'urlPicture' => $urlPicture,
            'urlMusic' => $urlMusic,
            //'result' => $result,
            'album' => isset($albumRender) ? $albumRender : false,
            'albumPicture' => isset($albumPicture) ? $albumPicture : false,
            'albumVideo' => isset($albumVideo) ? $albumVideo : false,
            'albumMusic' => isset($albumMusic) ? $albumMusic : false
        ]);
    }

    
    #[Route('/add-album', name: 'add_album')]
    public function album(User $user = null, Album $album = null, Request $request): Response
    {
        $auth = $this->security->getUser();
        
        if ($user != $auth) {
            $this->addFlash('error', "Vous navez pas le droit d'acceder a cette page");
            return $this->redirectToRoute('app_not_found');
        }
        $from = $request->query;
        $location = $from->get('from'); //recupere le paremetre indiquant dou on viens pour rediriger
        
        $album = new Album();
            
        $formAlbum = $this->createForm(AlbumFormType::class, $album);
        $formAlbum->handleRequest($request);

        if ($formAlbum->isSubmitted() && $formAlbum->isValid()) {
            
            $manager = $this->doctrine->getManager();
            $user->addAlbum($album);
            $manager->persist($user);
            $manager->persist($album);
            $manager->flush();
               
            $this->addFlash('success', "Votre album à bien eté crée");
            
            if ($location ===  'add_picture') {                             //$adress = $this->serverName;(('http://' || 'https://') . $adress . '/profil/' . $auth->getId() . '/media/add-picture')
                return $this->redirectToRoute('add_picture',['id' => $user->getId()]);
            } else if ($location === 'add_video') {
                return $this->redirectToRoute('add_video',['id' => $user->getId()]);
            } else if ($location === 'add_music') {
                return $this->redirectToRoute('add_music',['id' => $user->getId()]);
            } else if ($location === 'albums') {
                return $this->redirectToRoute('albums',['id' => $user->getId()]);
            }
        }
        return $this->render('user/profil/media/add_album.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'location' => $location, 
            'formAlbum' => isset($formAlbum) ? $formAlbum->createView() : false
        ]);
    }

    
    #[Route('/delete-album/{album}', name: 'delete_album_full')]
    public function deleteAlbum(User $user = null, Album $album): RedirectResponse 
    {
        $albumDeleting = $album->getId();
        
        if($albumDeleting) {
            $manager = $this->doctrine->getManager();
            
            $manager->remove($album);
            
            $manager->flush();
            $this->addFlash('success', "L'album a été supprimé avec succes");
        } else {
            
            $this->addFlash('error', "Erreur: album inexistante");
        }
        return $this->redirectToRoute('albums',['id' => $user->getId()]);     
    }


    #[Route('/delete-album-conserv/{album}', name: 'delete_album_no_content')]
    public function deleteAlbumConserv(User $user = null, Album $album, PictureRepository $pictureRepository, VideoRepository $videoRepository, MusicRepository $musicRepository): RedirectResponse 
    {
        $albumDeleting = $album->getId();
        
        if($albumDeleting) {
            
            $pictIn = $pictureRepository->findby(array('album' => $albumDeleting));
            $vidIn = $videoRepository->findby(array('album' => $albumDeleting));
            $musIn = $musicRepository->findby(array('album' => $albumDeleting));
            
            $i = 0;
            foreach ($pictIn as $albumDeleting) {
                $pictIn[$i]->setAlbum(null);
                $i++;
            }
            $i = 0;
            foreach ($vidIn as $albumDeleting) {
                $vidIn[$i]->setAlbum(null);
                $i++;
            }
            $i = 0;
            foreach ($musIn as $albumDeleting) {
                $musIn[$i]->setAlbum(null);
                $i++;
            }
            $manager = $this->doctrine->getManager();
            $manager->flush();
            //photo video music retire
            $manager->remove($album);
            $manager->flush();
            
            $this->addFlash('success', "L'album a été supprimé avec succes");
        } else {
            $this->addFlash('error', "Erreur: album inexistant");
        }
        return $this->redirectToRoute('albums',['id' => $user->getId()]);    
        
    }
    
    #[Route('/pictures-profil', name: 'pictures_album_profil')]
    public function pictureProfil(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $url = $this->getUrlPicture();

        $pictureRepository = $this->doctrine->getRepository(Picture::class);
        $mediaPicture = $pictureRepository->findBy(
            [
                'user' => $user->getId(),
                'profil' => '1'
            ],
            ['created_at' => 'DESC']
        );
        
        $auth = $this->security->getUser();

        return $this->render('user/profil/media/pictures_profil.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'url' => $url,
            'mediaPicture' => $mediaPicture,
            //'mediaButtonPicture' => 'mediaSelected'//tester utilité
        ]);
    }

    #[Route('/pictures', name: 'pictures')]
    public function picture(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $url = $this->getUrlPicture();

        $pictureRepository = $this->doctrine->getRepository(Picture::class);
        $mediaPicture = $pictureRepository->findBy(
            [
                'user' => $user->getId(),
                'profil' => null
            ],
            ['created_at' => 'DESC']
        );
        
        $auth = $this->security->getUser();

        return $this->render('user/profil/media/pictures.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'url' => $url,
            'mediaPicture' => $mediaPicture,
            //'mediaButtonPicture' => 'mediaSelected'//tester utilité
        ]);
    }

     
    #[Route('/add-picture', name: 'add_picture')]
    public function addPicture(User $user = null, Picture $picture = null, Album $album = null, Request $request, UploaderService $uploaderService/*, ApiService $apiService*/): Response
    { 
        $auth = $this->security->getUser();
        
        if ($user != $auth) {
            $this->addFlash('error', "Vous navez pas le droit d'acceder a cette page");
            return $this->redirectToRoute('app_not_found');
        }
        
        $picture = new Picture();

        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albumOption = $albumRepository->findBy([
            'user' => $user->getId(),
        ]);
    
        $form = $this->createForm(PictureFormType::class, $picture, ['album' => $albumOption]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() ) {
                    
            $newPicture = $form->get('picture')->getData();
            
            $manager = $this->doctrine->getManager();
            
            if ($newPicture) {
                $directory =  $this->getParameter('picture_directory');
                $picture->setStatus('1');
                $picture->setFilename($uploaderService->uploadFile($newPicture, $directory));
            }
            if (!empty($form->get('album')->getData())) {   //si un nom d'album a été choisi
                $album = $picture->getAlbum();
                $album->onPreUpdatet();     //rajoute updated_at danas lalbum  
                $manager->persist($album);  
            }
            $user->addPicture($picture);
            $manager->persist($picture);
            $manager->persist($user);   //enregistre picture associé a un user 
            $manager->flush();
                
            $this->addFlash('success', "Votre image à bien eté telechargé");
            return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "picture"]);    
        }
        return $this->render('user/profil/media/add_picture.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'form' => isset($form) ? $form->createView() : false,
        ]);
    }

    #[Route('/update-picture/{picture}', name: 'update_picture')]
    public function updatePicture(User $user = null, Request $request, Picture $picture/*, Album $album = null*/): Response
    { 
        $auth = $this->security->getUser();
        
        if ($user != $auth) {
            $this->addFlash('error', "Vous navez pas le droit d'acceder a cette page");
            return $this->redirectToRoute('app_not_found');
        } 
        $url = $this->getUrlPicture();
        
        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albumOption = $albumRepository->findBy([
            'user' => $user->getId(),
        ]);
        
        $formUpdate = $this->createForm(UpdatePictureFormType::class, $picture, ['album' => $albumOption]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted()) {
            if($formUpdate->isValid()) {
                $manager = $this->doctrine->getManager();
                $manager->flush();
                $this->addFlash('success', "Votre image à bien eté mise a jour");
                return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "picture"]);
            } 
        }  
            
        return $this->render('user/profil/media/update_picture.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'url' => $url,
            'picture' => $picture,
            'form' => $formUpdate->createView()//isset($form) ? $form->createView() : false,
        ]);
    }

    
    #[Route('/delete-picture/{picture}', name: 'delete_picture')]
    public function deletePicture(User $user = null, Picture $picture): RedirectResponse 
    {
        $url = $this->getUrlPicture();
        if ($picture) {
            $manager = $this->doctrine->getManager();
        
            $link = getcwd();
            unlink($link . '\uploads\pictures\\' . $picture->getFilename());
            $manager->remove($picture);
            $manager->flush();
            $this->addFlash('success', "La photo a été supprimé avec succes");
        } else {
            $this->addFlash('error', "Erreur: photo inexistante");
        }
        return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "picture"]); 
    }

    
    #[Route('/videos', name: 'videos')]
    public function video(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }

        $videoRepository = $this->doctrine->getRepository(Video::class);
        $mediaVideo = $videoRepository->findBy(['user' => $user->getId()], ['created_at' => 'DESC']);
        
        $auth = $this->security->getUser();

        return $this->render('user/profil/media/videos.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'mediaVideo' => $mediaVideo
        ]);
    }

    
    #[Route('/add-video', name: 'add_video')]
    public function addVideo(User $user = null, Video $video = null, Album $album = null, Request $request): Response
    { 
        $auth = $this->security->getUser();
        
        if ($user != $auth) {
            $this->addFlash('error', "Vous navez pas le droit d'acceder a cette page");
            return $this->redirectToRoute('app_not_found');
        }
        
        $video = new Video();

        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albumOption = $albumRepository->findBy([
            'user' => $user->getId(),
        ]);
            
        $form = $this->createForm(VideoFormType::class, $video, ['album' => $albumOption]);
        $form->handleRequest($request);
        
        $newVideo = $form->get('url')->getData();
        
        if ($form->isSubmitted() && $form->isValid() ) {
            
            $newVideo = $form->get('url')->getData();
            if (preg_match("/^<iframe [^>]+><\/iframe>$/", $newVideo) != 0) {       //iframe avec rien entre les 2 mais qquel dans la balise ouvrante
                $manager = $this->doctrine->getManager();
              
                if (!empty($form->get('album')->getData())) {   //si un nom d'album a été choisi
                    $album = $video->getAlbum();
                    $album->onPreUpdatet();     //rajoute updated_at danas lalbum  
                    $manager->persist($album);  
                }
                $video->setStatus('1');  
                $user->addVideo($video);
                $manager->persist($video);
                $manager->persist($user);   //enregistre picture associé a un user 
                $manager->flush();
                
                $this->addFlash('success', "Votre video à été ajouté avec succes");
                return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "video"]);    
                
            } else {
                $this->addFlash('error', "Une erreur est survenu lors du chargement de cette video");
                return $this->redirectToRoute('add_video',['id' => $user->getId()]);
            }
        }
        return $this->render('user/profil/media/add_video.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'form' => isset($form) ? $form->createView() : false,
        ]);
    }

    #[Route('/update-video/{video}', name: 'update_video')]
    public function updateVideo(User $user = null, Request $request, Video $video/*, Album $album = null*/): Response
    { 
        $auth = $this->security->getUser();
        
        if ($user != $auth) {
            $this->addFlash('error', "Vous navez pas le droit d'acceder a cette page");
            return $this->redirectToRoute('app_not_found');
        } 
        //$url = $this->getUrlPicture();
        
        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albumOption = $albumRepository->findBy([
            'user' => $user->getId(),
        ]);
        
        $formUpdate = $this->createForm(UpdateVideoFormType::class, $video, ['album' => $albumOption]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted()) {
            if($formUpdate->isValid()) {
                $manager = $this->doctrine->getManager();
            $manager->flush();
            $this->addFlash('success', "Votre video à bien eté mise a jour");
            return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "video"]);
            } 
        }  
            
        return $this->render('user/profil/media/update_video.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            //'url' => $url,
            'video' => $video,
            'form' => $formUpdate->createView()//isset($form) ? $form->createView() : false,
        ]);
    }

    #[Route('/delete-video/{video}', name: 'delete_video')]
    public function deleteVideo(User $user = null, Video $video): RedirectResponse 
    {
        if ($video) {
            $manager = $this->doctrine->getManager();
            $manager->remove($video);
            $manager->flush();
            $this->addFlash('success', "La video a été supprimé avec succes");
        } else {
            $this->addFlash('error', "Erreur: video inexistante");
        }
        return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "video"]); 
    }

    #[Route('/musics', name: 'musics')]
    public function music(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $urlMusic = $this->getUrlMusic();

        $musicRepository = $this->doctrine->getRepository(Music::class);
        $mediaMusic = $musicRepository->findBy(['user' => $user->getId()], ['created_at' => 'DESC']);
        
        $auth = $this->security->getUser();

        return $this->render('user/profil/media/music.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'urlMusic' => $urlMusic,
            'mediaMusic' => $mediaMusic,
            'mediaButtonMusic' => 'mediaSelected'
        ]);
    }

     #[Route('/add-music', name: 'add_music')]
    public function addMusic(User $user = null, Music $music = null, Album $album = null, Request $request, UploaderService $uploaderService/*, ApiService $apiService*/): Response
    { 
        $auth = $this->security->getUser();
        
        if ($user != $auth) {
            $this->addFlash('error', "Vous navez pas le droit d'acceder a cette page");
            return $this->redirectToRoute('app_not_found');
        }
        
        $music = new Music();

        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albumOption = $albumRepository->findBy([
            'user' => $user->getId(),
        ]);
    
        $form = $this->createForm(MusicFormType::class, $music, ['album' => $albumOption]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            
            $newMusic = $form->get('music')->getData();
           // var_dump($newMusic);die();
            $manager = $this->doctrine->getManager();
            
            if ($newMusic) {
                //$this->addFlash('success', "ici");
                $directory =  $this->getParameter('music_directory');
                $music->setStatus('1');
                $music->setFilename($uploaderService->uploadFile($newMusic, $directory));
            }
            if (!empty($form->get('album')->getData())) {   //si un nom d'album a été choisi
                $album = $music->getAlbum();
                $album->onPreUpdatet();     //rajoute updated_at danas lalbum  
                $manager->persist($album);  
            }
            $user->addMusic($music);
            $manager->persist($music);
            $manager->persist($user);   //enregistre picture associé a un user 
            $manager->flush();
                
            $this->addFlash('success', "Votre musique à bien eté telechargé");
            return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "music"]);    
        }
        return $this->render('user/profil/media/add_music.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'form' => isset($form) ? $form->createView() : false,
        ]);
    }

    #[Route('/update-music{music}', name: 'update_music')]
    public function updateMusic(User $user = null, Request $request, Music $music/*, Album $album = null*/): Response
    { 
        $auth = $this->security->getUser();
        
        if ($user != $auth) {
            $this->addFlash('error', "Vous navez pas le droit d'acceder a cette page");
            return $this->redirectToRoute('app_not_found');
        } 
        $url = $this->getUrlMusic();
        
        $albumRepository = $this->doctrine->getRepository(Album::class);
        $albumOption = $albumRepository->findBy([
            'user' => $user->getId(),
        ]);
        
        $formUpdate = $this->createForm(UpdateMusicFormType::class, $music, ['album' => $albumOption]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted()) {
            if($formUpdate->isValid()) {
                $manager = $this->doctrine->getManager();
            $manager->flush();
            $this->addFlash('success', "Votre musique à bien eté mise a jour");
            return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "music"]);
            } 
        }  
            
        return $this->render('user/profil/media/update_music.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'url' => $url,
            'music' => $music,
            'form' => $formUpdate->createView()//isset($form) ? $form->createView() : false,
        ]);
    }

    #[Route('/delete-music/{music}', name: 'delete_music')]
    public function deleteMusic(User $user = null, Music $music): RedirectResponse 
    {
        if ($music) {
            $manager = $this->doctrine->getManager();
            $manager->remove($music);
            $manager->flush();
            $this->addFlash('success', "La musique a été supprimé avec succes");
        } else {
            $this->addFlash('error', "Erreur: musique inexistante");
        }
        return $this->redirectToRoute('media',['id' => $user->getId(), 'origin' => "music"]); 
    }

}