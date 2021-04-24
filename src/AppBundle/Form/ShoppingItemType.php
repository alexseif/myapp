<?php

namespace AppBundle\Form;

use AppBundle\Entity\ShoppingItem;
use AppBundle\Entity\ShoppingList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('ShoppingList', EntityType::class, [
                'class' => ShoppingList::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'chosen',
                ]
            ])
            ->add('est')
            ->add('priority', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Low' => -1,
                    'Normal' => 0,
                    'Important' => 1,
                ],
                'expanded' => true,
                'label_attr' => ['class' => 'radio-inline']
            ])
            ->add('urgency', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Normal' => 0,
                    'Urgent' => 1
                ],
                'expanded' => true,
                'label_attr' => ['class' => 'radio-inline']
            ])
            ->add('eta', DateTimeType::class, array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false,
                'attr' => array(
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-MM-dd',
                )
            ))
            ->add('completed')
            ->add('completedAt', DateTimeType::class, array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShoppingItem::class,
        ]);
    }
}
