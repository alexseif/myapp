<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TasksType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('task')
                ->add('priority', ChoiceType::class, array(
                    'choices' => array(
                        1 => 'High',
                        0 => 'Normal',
                        -1 => 'Low',
                    ),
                    'expanded' => true,
                    'attr' => array(
                        'class' => 'btn-group btn-toggle',
                        "data-toggle"=>"buttons"
                        ),
                    'label_attr' => array('class' => 'btn btn-primary radio-inline')
                ))
                ->add('urgency', ChoiceType::class, array(
                    'choices' => array(
                        1 => 'Urgent',
                        0 => 'Not Urgent',
                    ),
                    'expanded' => true
                ))
                ->add('taskList', 'entity', array('class' => \AppBundle\Entity\TaskLists::class, 'choice_label' => 'name'))
                ->add('completed')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => \AppBundle\Entity\Tasks::class
        ));
    }

}
