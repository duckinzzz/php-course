<?php

namespace App\Controller\api;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route(name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        return $this->json(['data' => $tasks]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $task = new Task();

        $projectId = $data['project'] ?? null;

        if ($projectId) {
            $project = $projectRepository->find($projectId);

            if ($project) {
                $task->setProject($project);
            } else {
                return $this->json(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json(['error' => 'Project ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->json($task, Response::HTTP_CREATED, [], ['groups' => ['task_read']]);
        }

        return $this->json([
            'data' => [
                'errors' => (string)$form->getErrors(true, false),
            ]
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->json([
            'data' => [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'description' => $task->getDescription(),
                'createdAt' => $task->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updatedAt' => $task->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->json(['data' => $task]);
        }

        return $this->json([
            'data' => [
                'name' => (string) $form->getErrors(true, false),
                'description' => (string) $form->getErrors(true, false),
            ]
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->json(['data' => null], Response::HTTP_NO_CONTENT);
    }
}
