<?php

namespace AppBundle\Form;

use AppBundle\Entity\Scenario;
use AppBundle\Entity\ScenarioObjective;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScenarioObjectiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('value', MoneyType::class)
            ->add('priority', ChoiceType::class, array(
                'label' => false,
                'choices' => array(
                    'Low' => -1,
                    'Normal' => 0,
                    'Important' => 1,
                ),
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline')
            ))
            ->add('Urgency', ChoiceType::class, array(
                'label' => false,
                'choices' => array(
                    'Normal' => 0,
                    'Urgent' => 1
                ),
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline')
            ))
            ->add('scenario', EntityType::class, [
                'class' => Scenario::class,
                'choice_label' => 'title'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScenarioObjective::class,
        ]);
    }
}
