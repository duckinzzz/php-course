<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectsGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        return $this->json($projects, 200, [], ['groups' => ['project_read']]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProjectsGroupRepository $projectsGroupRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $project = new Project();

        $groupId = $data['projectGroup'] ?? null;

        if ($groupId) {
            $projectGroup = $projectsGroupRepository->find($groupId);

            if ($projectGroup) {
                $project->setProjectGroup($projectGroup);
            } else {
                return $this->json(['error' => 'Project group not found'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json(['error' => 'Project group ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->json($project, Response::HTTP_CREATED, [], ['groups' => ['project_read']]);
        }

        return $this->json([
            'data' => [
                'errors' => (string)$form->getErrors(true, false),
            ]
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->json($project, 200, [], ['groups' => ['project_read']]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->json($project, 200, [], ['groups' => ['project_read']]);
        }

        return $this->json([
            'data' => [
                'errors' => (string)$form->getErrors(true, false),
            ]
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['DELETE'])]
    public function delete(Project $project, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json(['data' => null], Response::HTTP_NO_CONTENT);
    }
}
