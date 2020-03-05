<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/panier", name="cart")
     */
    public function index()
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    /**
     * @Route("/cart_delete", name="cart_delete")
     */
    public function cartDelete(Request $request)
    {
        $request->getSession()->set('panier',[]);
        $this->addFlash('success', 'Le panier a bien été vidé');

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart_item_delete/{key}", name="cart_item_delete")
     */
    public function cartItemDelete($key, Request $request)
    {
        $panier = $request->getSession()->get('panier');
        array_splice($panier, $key, 1);
        $request->getSession()->set('panier', $panier);
        $this->addFlash('success', 'Le panier a bien été supprimé de panier');

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function cartAdd(Product $product, Request $request)
    {

        $qty = $request -> request->get('qty'); //$_POST['qty']
        $session = $request->getSession(); // on recupère la session

        if(!$session ->has('panier')){
            $session ->set('panier', []);// Dans la session on construit un array vide (panier) s'il n'existe pas déja
        }

        $panier = $session->get('panier');
        $panier[] = array(
            'product' => $product,
            'quantity' => $qty
        );

        $session ->set('panier', $panier);

        $this->addFlash('success',"Le produit <b>" .$product->getTitle(). "</b> a bien été ajouté au panier !<br>Vous pouvez <u><a href=".$this->generateUrl('home').">Continuer vos achats</a></u> 
                                                ou acceder au <u><a href=".$this->generateUrl('cart').">Panier</a></u>");

        return $this->redirectToRoute('product', [
            'slug' => $product -> getSlug(),
        ]);
    }
}
