<?php

namespace App\DataFixtures;

require_once __DIR__ . '/../../vendor/autoload.php';

use Faker;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use fzaninotto\faker\Factory;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\ProductOrder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;

    }


    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');

        $orderRepo = $manager -> getRepository(Order::class);
        $userRepo = $manager -> getRepository(User::class);
        $catRepo = $manager -> getRepository(Category::class);
        $productRepo = $manager -> getRepository(Product::class);

        for($i = 1; $i <= 20; $i++){
            $user = new User;
            $user -> setUsername($faker -> username);
            $user -> setLastname($faker -> lastName);
            $user -> setEmail($faker -> email);
            $user -> setFirstname($faker -> firstName);
            $user -> setSexe($faker -> randomElement(['m', 'f']));
            $user -> setCity($faker -> city);
            $user -> setAddress($faker -> address);
            $user -> setPassword($this->encoder->encodePassword($user, '123456'));
            $user -> setZipCode(rand(11111, 99999));
            $user -> setBirthday($faker -> dateTimeBetween('-50 years', '-16 years', 'Europe/paris'));
            $user -> setRegisterDate(new \DateTime('now'));
            $user -> setTel($faker -> e164PhoneNumber);

            $manager -> persist($user);
        }
        $manager->flush();






        for($i = 1; $i <= 20; $i++){
            $product  = new Product;

//            $product -> setTitle(implode(' ', $faker -> words(3)));
            $product -> setTitle('Air Jordan'.$i);
            $product -> setPrice($faker -> randomFloat(2, 120, 280));
            $product -> setStock(rand(1, 150));
            $product -> setBrand($faker -> randomElement(['Jordan1', 'Jordan2', 'Jordan3', 'Jordan3', 'Jordan4', 'Jordan5', 'Jordan6']));
            $product -> setDescription($faker -> text(200));
            $product -> setImage('product_' . $i . '.jpg');
            $product -> setSize($faker -> randomElement(['4', '5', '6', '7', '8', '9', '10']) . $faker -> randomElement(['', '.5']));
            $product -> setColor($faker -> randomElement(['Blanc', 'Rouge', 'Noir', 'Beige', 'Vert', 'Rose']));

            $manager -> persist($product);
        }
        $manager->flush();

        $cat = array('Jordan1', 'Jordan2', 'Jordan3', 'Jordan3', 'Jordan4', 'Jordan5', 'Jordan6');

        foreach($cat as $c){
            $z = rand(1, 5);
            $category = new Category;
            $category -> setTitle($c);
            $category -> setKeywords(implode(', ', $faker -> words($z)));
            $manager -> persist($category);
        }
        $manager->flush();

        //---------

        //Category et product
        $products = $productRepo -> findAll();
        $categories = $catRepo -> findAll();
        $users = $userRepo -> findAll();


        foreach($products as $p){
            $nb = rand(1, 5);

            for($j = 1; $j <=  $nb; $j++){
                $liste = array_keys($categories);
                $indice = $faker -> randomElement($liste);
                $p -> addCategory($categories[$indice]);
                array_splice($liste, $indice , 1);
            }
            $manager -> persist($p);
        }
        $manager -> flush();








        for($k = 1; $k <= 10; $k++){


            $order = new Order();

            $order -> setDate($faker -> dateTimeBetween('-2 years', 'now', 'Europe/paris'));
            $order -> setUser($faker -> randomElement($users));
            $order -> setState(rand(1, 6));
            $order -> setAmount($faker -> randomFloat(2, 200, 500));

            $nbP = rand(1, 5);

            for($v = 1; $v <= $nbP; $v++){
                $qte = rand(1,3);
                $po = new ProductOrder;
                $po -> setProduct($faker -> randomElement($products));
                $po -> setQuantity($qte);
                $po -> setCde($order);

                $manager -> persist($po);
                //$order -> addProductOrder($po);
            }
            $manager -> persist($order);
        }
        $manager -> flush();
    }
}
