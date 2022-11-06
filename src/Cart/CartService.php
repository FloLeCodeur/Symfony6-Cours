<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService 
{

  protected $requetStack;
  protected $productRepository;

  public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
  {
    $this->requetStack = $requestStack;
    $this->productRepository = $productRepository;
  }

  protected function getCart(): array {
    return $this->requetStack->getSession()->get('cart', []);
  }

  protected function saveCart(array $cart) {
    $this->requetStack->getSession()->set('cart', $cart);
  }

  public function add(int $id) 
  {

    // 1. Retrouver le panier dans la session (sous forme de tableau)
    // 2. Si il n'existe pas encore, alors prendre un tableau vide

    $cart = $this->getCart();

    // 3. Voir si le produit ($id) existe déjà dans le tableau
    // 4. Si c'est le cas, simplement augmenter la quantité
    // 5. Sinon, ajouter le produit avec la quantité 1

    if (!array_key_exists($id, $cart)) {
        $cart[$id] = 0;
    } 
        $cart[$id] ++;

    // 6. Enregistrer le tableau mis à jour dans la session
    $this->saveCart($cart);
  }

  public function remove(int $id) {
    $cart = $this->getCart();

    unset($cart[$id]);

    $this->saveCart($cart);
  }

  public function decrement(int $id) {
    $cart = $this->getCart();

    if (!array_key_exists($id, $cart)) {
      return;
    }

    // Soit le produit est à 1 alors il faut simplement le supprimer
    if ($cart[$id] === 1) {
      $this->remove($id);
      return;
    }

    // Soit le produit est à plus de 1, alors il faut décrémenter
    $cart[$id]--;
    $this->saveCart($cart);
  }

  public function getTotal() : int {
    $total = 0;

    foreach ($this->requetStack->getSession()->get('cart', []) as $id => $qty) {
      $product = $this->productRepository->find($id);

      if (!$product) {
        continue;
      }

      $total += (($product->getPrice() * $qty) / 100);
    }

    return $total;
  }

  public function getDetailedCartItems() : array {
    $detailedCart = [];

        foreach ($this->requetStack->getSession()->get('cart', []) as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
              continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
  }
}