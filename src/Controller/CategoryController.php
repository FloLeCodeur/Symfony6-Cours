<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList() {
        // 1. Aller chercher les catégories dans la base de données (repository)

        $categories = $this->categoryRepository->findAll();

        // 2. Renvoyer le rendu HTML sous la forme d'une Response ($this->render)

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/category/create', name: 'catgeory_create')]
    public function createCategory(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {

        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->persist(($category));
            $em->flush();


            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }
    
    #[Route('/admin/category/{id}/edit', name: 'catgeory_edit')]
    // #[IsGranted("ROLE_ADMIN", message: "Vous n'avez pas le droit d'accéder à cette ressource")]
    public function editCategory(Request $request, $id, CategoryRepository $categoryRepository, EntityManagerInterface $em, Security $security) 
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous n'avez pas le droit d'accéder à cette ressource");

        // // $user = $security->getUser();
        // $user = $this->getUser();

        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }

        // // if(!in_array("ROLE_ADMIN", $user->getRoles())){
        // //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource");
        // // }
        
        // if($this->isGranted("ROLE_ADMIN") === false){
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource");
        // }

        $category = $categoryRepository->find($id);



        // -- Voter CategoryVoter -- 

        // if (!$category) {
        //     throw new NotFoundHttpException("Cette catégorie n'existe pas");
        // }

        // $security->isGranted('CAN_EDIT', $category);

        // $this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le propriétaire de cette catégorie");



        // $user = $this->getUser();

        // if (!$user) {
        //     return $this->redirectToRoute('security_login');
        // }

        // if ($user !== $category->getOwner()) {
        //     throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
        // }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();
        
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
    
}
