<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private const int NB_ARTICLES = 150;

    private const array CATEGORIES = [
        "Sport",
        "Politique",
        "Santé",
        "Technologie",
        "Économie"
    ];

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = [];

        foreach (self::CATEGORIES as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $title = $faker->realText(50);
            $slug = strtolower($this->slugger->slug($title));

            $article = new Article();
            $article->setTitle($title)
                ->setSlug($slug)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', 'now')))
                ->setContent($faker->paragraphs($faker->numberBetween(3, 9), true))
                ->setVisible($faker->boolean(80))
                ->setCategory($faker->randomElement($categories))
            ;
            $manager->persist($article);
        }

        $manager->flush();
    }
}
