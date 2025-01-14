<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
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
        return $this->json($projects);
    }

    #[Route('/new', name: 'app_project_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->json($project, Response::HTTP_CREATED);
        }

        return $this->json([
            'errors' => (string)$form->getErrors(true, false),
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->json($project);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->json($project);
        }

        return $this->json([
            'errors' => (string)$form->getErrors(true, false),
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['DELETE'])]
    public function delete(Project $project, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
