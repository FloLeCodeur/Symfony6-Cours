<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseDetails;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PurchaseConfirmationController extends AbstractController
{

  // protected $formFactory;
  // protected $router;
  // protected $security;
  protected $cartService;
  protected $em;

  public function __construct( CartService $cartService, EntityManagerInterface $em)
  {
    // $this->formFactory = $formFactory;
    // $this->router = $router;
    // $this->security = $security;
    $this->cartService = $cartService;
    $this->em = $em;
  }

  #[Route('/purchase/confirm', name:'purchase_confirm')]
  #[IsGranted("ROLE_USER", message:"Vous devez être connecté pour confirmer une commande")]
  public function confirm(Request $request) {
    // 1. Nous voulons lire les données du formulaire (FormFactoryInterface / Request)

    $form = $this->createForm(CartConfirmationType::class);
    // $form = $this->formFactory->create(CartConfirmationType::class);
    
    $form->handleRequest($request);

    // 2. Si le formulaire n'a pas été soumis : dégager
    if (!$form->isSubmitted()) {
      $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
      // $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
      return $this->redirectToRoute('cart_show');
    }


    // 3. Si je ne suis pas connecté : dégager
    $user = $this->getUser();

    // if (!$user) {
    //   throw new AccessDeniedException("Vous devez être connecté pour confirmer une commande");
    // }
    
    // 4. Si il n'y a pas de produits dans mon panier : dégager (CartService)
    $cartItems = $this->cartService->getDetailedCartItems();

    if (count($cartItems) === 0) {
      $this->addFlash('warning', 'Vous ne pouvez confirmer une commande avec un panier vide');
      return $this->redirectToRoute('cart_show');
    }

    // 5. Nous allons créer une purchase
    /** @var Purchase */
    $purchase = $form->getData();

    // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)
    $purchase->setUser($user)
      ->setPurchasedAt(new DateTime())
      ->setTotal($this->cartService->getTotal());

    $this->em->persist($purchase);

    // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)
    foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
      $purchaseItem = new PurchaseDetails;
      $purchaseItem->setPurchase($purchase)
        ->setProducts($cartItem->product)
        ->setProductName($cartItem->product->getPrice())
        ->setQuantity($cartItem->qty)
        ->setTotal($cartItem->getTotal())
        ->setProductPrice($cartItem->product->getPrice());

        $this->em->persist($purchaseItem);
    }

    // 8. Nous allons enregistrer la commande (EntityManagerInterface)
    $this->em->flush();

    $this->cartService->empty();

    $this->addFlash('success', 'La commande à bien était enregistrée');
    return $this->redirectToRoute('purchase_index');
  }

}