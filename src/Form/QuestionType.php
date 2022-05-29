<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('header', TextType::class, [
                'label' => 'Your question',
                'constraints' => [
                    new Length([
                        'max' => 255
                    ]),
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new Length([
                        'max' => 1024
                    ]),
                ]
            ])
            ->add('category', TextType::class, [
                'label' => 'Tag',
                'constraints' => [
                    new Length([
                        'max' => 48
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
