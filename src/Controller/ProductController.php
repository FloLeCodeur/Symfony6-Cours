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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority: -1)]
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

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority: -1)]
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
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator) {
     
        // -- Validator d'un tableau --

        // $client = [
        //     'nom' => '',
        //     'prenom' => 'Lior',
        //     'voiture' => [
        //         'marque' => '',
        //         'couleur' => 'Noire'
        //     ]
        // ];

        // $collection = new Collection([
        //     'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide !"]),
        //     'prenom' => [
        //         new NotBlank(['message' => "Le prénom ne doit pas être vide"]),
        //         new Length(['min' => 3, 'minMessage' => "Le prénom ne doit pas faire moins de 3 caractères"])
        //     ],
        //     'voiture' => new Collection([
        //         'marque' => new NotBlank(['message' => "La marque de la voiture est obligatoire"]),
        //         'couleur' => new NotBlank(['message' => "La couleur de la voiture est obligatoire"])
        //     ])
        // ]);

        // $results = $validator->validate($client, $collection);
        
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product, 
            // ['validation_groups' => ["Default", "with-price"]]
        );

        // $form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            if ($form->isSubmitted() && $form->isValid()) {
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
