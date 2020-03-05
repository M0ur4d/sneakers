<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin/product/", name="admin_product")
     */
    public function admin_product(ProductRepository $pRepo, CategoryRepository $cRepo)
    {
        $products = $pRepo->findAll();
        $categories = $cRepo->findAll();

        return $this->render('admin/product_list.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/product/add", name="admin_product_add")
     */
    public function admin_product_add()
    {
        return $this->render('admin/product_form.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/product/update/{id}", name="admin_product_update")
     */
    public function admin_product_update()
    {
        return $this->render('admin/product_form.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="admin_product_delete")
     */
    public function admin_product_delete()
    {
        return $this->render('admin/product_list.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
