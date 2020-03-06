<?php

namespace App\Controller;

use App\Entity\Product;
use Flosch\Bundle\StripeBundle\Stripe\StripeClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        // Modifier la quantité d'un produit si il existe deja (eviter les doublons en lignes)

        # Recuperer la liste des identitifiants des produits du panier
        $id_produits = array_map(function ($prod){
            return $prod['product']->getId();
        }, $panier);
        # Rechercher la clé du produit à ajouter au panier (si déjà existant)
        $cle_produit = array_search($product->getId(),$id_produits);

        # Si une clé à été trouvée (si le produit à ajouter existe deja dans le panier)
        if($cle_produit !== false){
            # On incremente la quantité
            $panier[$cle_produit]['quantity'] += $qty;
        }else{
            # Sinon, on ajoute une nouvelle entrée dans le panier
            $panier[] = [
                'product' => $product,
                'quantity' => $qty
            ];
        }

        $session ->set('panier', $panier);

        $this->addFlash('success',"Le produit <b>" .$product->getTitle(). "</b> a bien été ajouté au panier !<br>Vous pouvez <u><a href=".$this->generateUrl('home').">Continuer vos achats</a></u> 
                                                ou acceder au <u><a href=".$this->generateUrl('cart').">Panier</a></u>");

        return $this->redirectToRoute('product', [
            'slug' => $product -> getSlug(),
        ]);
    }

    /**
     * Modifier la quantité d'un produit dans le panier
     * @Route("/cart/update-quantity/{id}", name="cart_item_update_quantity")
     */
    public function cartUpdateQuantity(Product $product, Request $request, SessionInterface $session)
    {
        # quantité souhaité
        $qt = $request->request->get('qt', 1);
        $panier = $session->get('panier');



        # Rechercher le produit dans le panier
        $id_produits = array_map(function ($prod){
            return $prod['product']->getId();
        }, $panier);

        $cle_produit = array_search($product->getId(), $id_produits);

        # si la quantité n'est pas un entier positif, on supprime le produit du panier
        if((!ctype_digit($qt) || $qt < 1) && $cle_produit !== false){
            return $this->redirectToRoute('cart_item_delete', ['key' => $cle_produit]);
        }

        # mettre a jour les quantités
        if($cle_produit !== false){
            $panier[$cle_produit]['quantity'] = $qt;
            $session->set('panier', $panier);
            $this->addFlash('success', sprintf(
                'La quantité du produit "%s" a été modifié',
                    $product->getTitle()
            ));
        }

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/checkout", name="cart_checkout")
     * @IsGranted("ROLE_USER")
     */
    public function cartCheckout(SessionInterface$session, StripeClient $stripe)
    {
        // Calculer le montant de la commande
        $panier = $session->get('panier',[]);
        if ($panier === []){
            return $this->redirectToRoute('home');
        }

        $total = 0;
        foreach($panier as $achat){
            $total += $achat['product']->getPrice() * $achat['quantity'];
        }

        return $this->render('order/checkout.html.twig', [
            'total' => $total,
        ]);
    }

}
