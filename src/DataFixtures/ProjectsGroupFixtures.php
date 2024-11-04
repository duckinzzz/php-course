<?php

namespace App\DataFixtures;

use App\Entity\ProjectsGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectsGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $group = new ProjectsGroup();
            $group->setName('Group #' . $i);
            $manager->persist($group);
        }

        $manager->flush();
    }
}
