<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    const BOOKS = [
        'Giacomo Guilizzoni Founder & CEO' => 'Olivier Norek',
        'Marco Botton Tuttofare' => 'Michel Bussi',
        'Mariah Maclachlan Better Half' => 'Michel Bussi',
        'Valerie Liberty Head Chef' => 'Valérie Perrin',
        'Livre 5' => 'Olivier Norek',
        'Livre 6' => 'Michel Bussi',
        'Livre 7' => 'Michel Bussi',
        'Livre 8' => 'Valérie Perrin',
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::BOOKS as $title => $author){
            $book = new Book();
            $book->setTitle($title);
            $book->setAuthor($author);

            $manager->persist($book);
        }
        $manager->flush();
    }
}
