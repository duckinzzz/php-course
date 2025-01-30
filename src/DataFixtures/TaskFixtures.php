<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projects = $manager->getRepository(Project::class)->findAll();

        if (empty($projects)) {
            throw new \LogicException('Необходимо загрузить сначала ProjectFixtures');
        }

        foreach ($projects as $project) {
            for ($i = 0; $i < 10; $i++) {
                $task = new Task();
                $task->setName('Task #' . $i);
                $task->setDescription('Description of Task #' . $i);
                $task->setProject($project);
                $manager->persist($task);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }
}
