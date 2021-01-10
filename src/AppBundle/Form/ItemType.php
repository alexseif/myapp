<?php

namespace AppBundle\Form;

use AppBundle\Definition\Types;
use AppBundle\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Model\Type;

class ItemType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('sort')
            ->add('priority', ChoiceType::class, [
//                'expanded' => true,
                'label_attr' => ['class' => 'radio-inline'],
                'choices' => [
                    'Normal' => 0,
                    'Important' => 1,
                    'Urgent' => 2,
                    'Important & Urgent' => 3
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }

}
