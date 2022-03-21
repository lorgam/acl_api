<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private array $currencies;

    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }

    public function load(ObjectManager $manager): void
    {
        // Categories
        $categories = [];
        for ($i = 0; $i < 25; $i++) {
            $category = new Category();
            $category->setName("category{$i}");
            $category->setDescription("category desccription: {$i}");
            $manager->persist($category);

            $categories[] = $category;
        }

        // Products
        for ($i = 0; $i < 190; $i++) {
            $product = new Product();
            $product->setName("product{$i}");
            $product->setPrice(1000 * (mt_rand() / mt_getrandmax()));
            if (boolval(rand(0, 1))) {
                $product->setCategory($categories[rand(0, count($categories) - 1)]);
            }
            $product->setCurrency($this->currencies[rand(0, count($this->currencies) - 1)]);
            $product->setFeatured(boolval(rand(0, 1)));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
