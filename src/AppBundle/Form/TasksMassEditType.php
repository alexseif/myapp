<?php

namespace AppBundle\Form;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TasksMassEditType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priority', ChoiceType::class, array(
                'choices' => [
                    'Low' => -1,
                    'Normal' => 0,
                    'Important' => 1,
                ],
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline')
            ))
            ->add('urgency', ChoiceType::class, array(
                'choices' => array(
                    'Not Urgent' => 0,
                    'Urgent' => 1,
                ),
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline')
            ))
            ->add('taskList', EntityType::class, array('class' => TaskLists::class, 'choice_label' => 'name'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Tasks::class
        ));
    }

}
