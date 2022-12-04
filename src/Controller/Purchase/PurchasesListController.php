<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
  protected $security;
  protected $router;
  protected $twig;

  public function __construct(Security $security, RouterInterface $router, Environment $twig)
  {
    $this->security = $security;
    $this->router = $router;
    $this->twig = $twig;
  } 

  #[Route("/purchases", name:'purchase_index')]
  public function index()
  {
    // 1. Nous devons nous assurer que la personne est connectée (sinon redirection vers page d'acceuil) -> Security

    /** @var User */
    $user = $this->security->getUser();

    if (!$user) {
      // Redirection -> RedirectResponse

      $url = $this->router->generate('security_login');
      return new RedirectResponse($url);


      // throw new AccessDeniedException("Vous devez être connecté pour voir vos commandes");

      // Générer une URL en fonction du nom d'une route -> UrlGenerator Router Interface
    }

    // 2. Nous voulons savoir QUI est connecté -> Security

    // 3. Nous voulons passer l'utilisateur connecté à twig afin d'afficher 
    $html = $this->twig->render('purchase/index.html.twig', [
      'purchases' => $user->getPurchases()
    ]);

    return new Response($html);
  }
}