<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category')]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {

        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie n'existe pas");
            
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show')]
    public function show($slug, ProductRepository $productRepository) {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em) {
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        // $form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $em->flush();

            // $response = new Response();                                          // Première option
            // $url = $urlGenerator->generate('product_show', [                     // Route à la main
            //     'category_slug' => $product->getCategory()->getSlug(),           // Route à la main
            //     'slug' => $product->getSlug()                                    // Route à la main
            // ]);
            // $response->headers->set('Location', $url);                           // Première option
            // $response->setStatusCode(302);                                       // Première option

            // $response = new RedirectResponse($url);                              // Deuxième option

            // return $this->redirect($url);                                        // Route à la main

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em) {

        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

            // $form = $builder->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $product->setSlug(strtolower($slugger->slug($product->getName())));

                $em->persist($product);
                $em->flush();

                // $product = new Product;
                // $product->setName($data['name'])
                //     ->setShortDescription($data['shortDescription'])
                //     ->setPrice($data['price'])
                //     ->setCategory($data['category']);
                
                
                return $this->redirectToRoute('product_show', [
                    'category_slug' => $product->getCategory()->getSlug(),
                    'slug' => $product->getSlug()
                ]);
            }


            $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}