<?php 
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class NewTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'jump' => 'jump',
                    'rotation' => 'rotation',
                    'press' => 'press',
                    'grab' => 'grab',
                    'rail' => 'rail',
                ]
            ])
            ->add('youtube_link', TextareaType::class, [
                'required' => false,
                'label' => "Youtube link (https://www.youtube.com/watch?v=xxxxxxxxxxx) (optional)",
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'label' => 'Image (optional)'
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Trick']);
    }    
}
