<?php 
namespace App\Form\Type;

use App\Entity\Media;
use Doctrine\ORM\Mapping\Entity;
use Masterminds\HTML5\Entities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class FeaturedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('featuredImage', EntityType::class, [
                'class' => Media::class,
                'choices' => $options['data'],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => array(
                    '0' => array('class' => 'class_one'),
                    '1' => array('class' => 'class_two'),
                )
            ])
            ->add('save', SubmitType::class, ['label' => 'Save Featured Image']);
            
    }    
}
