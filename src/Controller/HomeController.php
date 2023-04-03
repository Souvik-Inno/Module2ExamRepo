<?php

namespace App\Controller;

use App\Entity\App;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Controller defined which sets all the routes in the project.
 */
class HomeController extends AbstractController {

  /**
   *  Function renders the Login page by default.
   * 
   *  @Route("", name="homepage")
   *    Route defined to route to homepage by default.
   * 
   *  @return Response
   *    Returns homepage as response.
   */
  public function defaultRoute(): Response {
    return $this->render('home/index.html.twig', [
      'controller_name' => 'HomeController',
      'errormessage' => '',
    ]);
  }

  /**
   *  Function renders the Feed page after user is logged in.
   * 
   *  @Route("/feed", name="feedpage")
   *    Route defined to route to feed page.
   * 
   *  @param Request $request
   *    Gets data from form.
   *  @param EntityManagerInterface $entityManager
   *    Used to load and retrieve data from Entity.
   *  @param SessionInterface $si
   *    Used to load and retrieve data from session.
   * 
   *  @return Response
   *    Returns feedpage as response.
   */
  public function feedRoute(Request $request, EntityManagerInterface $entityManager, SessionInterface $si): Response {
    if (isset($_POST['loginSubmit'])) {
      $uservalue = $entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('loginEmail'), 'password' => $request->get('loginPass'), 'privilege' => $request->get('privilege')]);
      if (!$uservalue) {
        return $this->render('home/index.html.twig', [
          'controller_name' => 'HomeController',
          'errormessage' => 'Wrong credentials provided',
        ]);
      }
      $admin = FALSE;
      if ($request->get('privilege') == "Admin") {
        $admin = TRUE;
      }
      $si->set("email", $request->get('loginEmail'));
      $si->set("logged", TRUE);
    }
    else {
      $uservalue = $entityManager->getRepository(User::class)->findOneBy(['email' => $si->get('email')]);
      if ($uservalue->getPrivilege() == "Admin") {
        $admin = TRUE;
      }
      else {
        $admin = FALSE;
      }
    }


    $apps = $entityManager->getRepository(App::class)->findAll();

    return $this->render('home/feed.html.twig', [
      'controller_name' => 'HomeController',
      'errormessage' => '',
      'admin' => $admin,
      'apps' => $apps,
    ]);
  }

  /**
   *  Function uploads app from the admin.
   * 
   *  @Route("/upload", name="upload")
   *    Route defined to upload app using ajax.
   * 
   *  @param Request $request
   *    Gets data from form.
   *  @param EntityManagerInterface $entityManager
   *    Used to load and retrieve data from Entity.
   * 
   *  @return JsonResponse
   *    Returns new app info as JsonResponse.
   */
  public function uploadApp(Request $request, EntityManagerInterface $entityManager): JsonResponse {
    $data = new App;
    $data->setAppName($request->get('app_name'));
    $data->setDescription("Not too much time");
    $data->setDeveloper("Souvik Banerjee");
    $data->setDownloadCount(0);

    if ($request->files->get('image') !== NULL) {
			$image = $request->files->get('image');
			$extension = $image->guessExtension();
			$newFileName = uniqid() . "." . $extension;
			try {
				$image->move(
					$this->getParameter('kernel.project_dir') . '/public/uploads/',
					$newFileName
				);
			} catch (FileException $e) {
				dd($e);
			}
			$data->setImage('/uploads/' .$newFileName);
		}
    $entityManager->persist($data);
		$entityManager->flush();
    return new JsonResponse([
      'id' => $data->getId(),
      'name' => $data->getAppName(),
      'description' => $data->getDescription(),
      'image' => $data->getImage(),
      'developer' => $data->getDeveloper(),
      'downloadCount' => $data->getDownloadCount(),
    ]);
  }

  /**
   *  Function downloads app and adds download count using ajax.
   * 
   *  @Route("/downloadApp", name="downloadApp")
   *    Route defined to route to homepage by default.
   * 
   *  @param Request $request
   *    Gets data from form.
   *  @param EntityManagerInterface $entityManager
   *    Used to load and retrieve data from Entity.
   *  @param SessionInterface $si
   *    Used to load and retrieve data from session.
   * 
   *  @return JsonResponse
   *    Returns Download Count.
   */
  public function downloadApp(Request $request, EntityManagerInterface $entityManager, SessionInterface $si): JsonResponse {
    $appId = $request->get('rowId');
    $appvalue = $entityManager->getRepository(App::class)->findOneBy(['id' => $appId]);

    $downloads = $appvalue->getDownloadCount() + 1;
    $appvalue->setDownloadCount($downloads);
    $uservalue = $entityManager->getRepository(User::class)->findOneBy(['email' => $si->get('email')]);

    $appvalue->addUser($uservalue);
    $entityManager->persist($appvalue);
    $uservalue->addApp($appvalue);
    $entityManager->persist($uservalue);
    $entityManager->flush();
    return new JsonResponse([
      'downloads' => $downloads,
    ]);
  }

  /**
   *  Function renders the Profile Page.
   * 
   *  @Route("/profile", name="profile")
   *    Route defined to route to homepage by default.
   * 
   *  @return Response
   *    Returns homepage as response.
   */
  public function profilePage(EntityManagerInterface $entityManager, SessionInterface $si): Response {

    $uservalue = $entityManager->getRepository(User::class)->findOneBy(['email' => $si->get('email')]);
    $apps = $uservalue->getApps();

    return $this->render('home/profile.html.twig', [
      'controller_name' => 'HomeController',
      'errormessage' => '',
      'apps' => $apps,
    ]);
  }

  /**
   *  Function downloads app and adds download count using ajax.
   * 
   *  @Route("/review", name="review")
   *    Route defined to route to homepage by default.
   * 
   *  @param Request $request
   *    Gets data from form.
   *  @param EntityManagerInterface $entityManager
   *    Used to load and retrieve data from Entity.
   *  @param SessionInterface $si
   *    Used to load and retrieve data from session.
   * 
   *  @return JsonResponse
   *    Returns Download Count.
   */
  public function reviewShare(Request $request, EntityManagerInterface $entityManager, SessionInterface $si): JsonResponse {
    $uservalue = $entityManager->getRepository(User::class)->findOneBy(['email' => $si->get('email')]);
    $appId = $request->get('rowId');
    $appvalue = $entityManager->getRepository(App::class)->findOneBy(['id' => $appId]);
    $review = $request->get('review');
    $reviewData = new Review;
    $reviewData->setText($review);
    $reviewData->setPost($appvalue);
    $reviewData->setUser($uservalue);
    $entityManager->persist($reviewData);
    $entityManager->flush();

    return new JsonResponse([
      'review' => $review,
    ]);
  }


}
