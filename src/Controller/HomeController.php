<?php 

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

  /**
   *  @Route("/", name="homepage")
   */
  public function homePage(ProductRepository $productRepository) {

    $product = $productRepository->findBy([], [], 3);

    dump($product);
    
    return $this->render('home.html.twig', ['products' => $product]);
  }
}