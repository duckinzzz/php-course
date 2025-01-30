<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\ProjectsGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projectsGroups = $manager->getRepository(ProjectsGroup::class)->findAll();

        if (empty($projectsGroups)) {
            throw new \LogicException('Необходимо загрузить сначала ProjectsGroupFixtures');
        }

        foreach ($projectsGroups as $group) {
            for ($i = 0; $i < 3; $i++) {
                $project = new Project();
                $project->setName('Project #' . $i);
                $project->setProjectGroup($group);
                $manager->persist($project);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectsGroupFixtures::class,
        ];
    }
}
