<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for($i=1;$i<20;$i++){
            $post = new Post();
            $post->setTitle('my fake title'.$i);
            $post->setContent('my fake Content'.$i);
            $post->setPublished(new \Datetime());
            $post->setAuthor('my fake Author'.$i);
            $post->setSlug('my fake Slug'.$i);

            $manager->persist($post);
        }

        $manager->flush();
    }
}
