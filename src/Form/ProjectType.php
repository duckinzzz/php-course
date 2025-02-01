<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectsGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Name should be at least 3 characters long',
                        'maxMessage' => 'Name should be at most 255 characters long',
                    ]),
                ],
            ])
            ->add('projectGroup', EntityType::class, [
                'class' => ProjectsGroup::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotNull([
                        'message' => 'project group should not be null',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'csrf_protection' => false,
        ]);
    }
}
