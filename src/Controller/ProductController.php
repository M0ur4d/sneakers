<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Category;
use Knp\Component\Pager\PaginatorInterface;


class ProductController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $pRepo = $this->getDoctrine()->getRepository(Product::class);
        $cRepo = $this->getDoctrine()->getRepository(Category::class);

//        $donnees = $pRepo->findAll();
        $products = $pRepo->findAll();
        $categories = $cRepo->findAll();

//        $products = $paginator->paginate(
//            $donnees, // Requête contenant les données à paginer (ici nos articles)
//            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
//            6 // Nombre de résultats par page
//        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product")
     */
    public function products(Product $product)
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,

        ]);
    }

    /**
     * @Route("/category/{slug}", name="category")
     */
    public function category(Category $category, PaginatorInterface $paginator, Request $request)
    {
        $cRepo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $cRepo->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $category->getProducts(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/recherche/", name="recherche")
     */
    public function search(Request $request)
    {
        $term = $request->query->get('s');

        $pRepo = $this->getDoctrine()->getRepository(Product::class);
        $cRepo = $this->getDoctrine()->getRepository(Category::class);

        $products = $pRepo->findByTerm($term);
        $categories = $cRepo->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
