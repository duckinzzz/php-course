<?php

namespace App\Controller\api;

use App\Entity\ProjectsGroup;
use App\Form\ProjectsGroupType;
use App\Repository\ProjectsGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projects/group')]
final class ProjectsGroupController extends AbstractController
{
    #[Route(name: 'app_projects_group_index', methods: ['GET'])]
    public function index(ProjectsGroupRepository $projectsGroupRepository): Response
    {
        $projectsGroups = $projectsGroupRepository->findAll();
        return $this->json(['data' => $projectsGroups]);
    }

    #[Route('/new', name: 'app_projects_group_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $projectsGroup = new ProjectsGroup();

        $form = $this->createForm(ProjectsGroupType::class, $projectsGroup);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projectsGroup);
            $entityManager->flush();

            return $this->json(['data' => $projectsGroup], Response::HTTP_CREATED);
        }

        return $this->json([
            'data' => [
                'name' => (string)$form->getErrors(true, false),
            ]
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_projects_group_show', methods: ['GET'])]
    public function show(ProjectsGroup $projectsGroup): Response
    {
        return $this->json($projectsGroup, 200, [], ['groups' => ['projects_group_read']]);
    }

    #[Route('/{id}/edit', name: 'app_projects_group_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, ProjectsGroup $projectsGroup, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ProjectsGroupType::class, $projectsGroup);
        $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectsGroup->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->json(['data' => $projectsGroup]);
        }

        return $this->json([
            'data' => [
                'name' => (string)$form->getErrors(true, false),
            ]
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_projects_group_delete', methods: ['DELETE'])]
    public function delete(ProjectsGroup $projectsGroup, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($projectsGroup);
        $entityManager->flush();

        return $this->json(['data' => null], Response::HTTP_NO_CONTENT);
    }
}
