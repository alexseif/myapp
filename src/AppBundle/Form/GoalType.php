<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoalType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('name')
        ->add('requirements', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, array(
          'entry_type' => \Symfony\Component\Form\Extension\Core\Type\TextType::class,
          'entry_options' => array(
            'label' => false
          ),
          'allow_add' => true,
          'allow_delete' => true,
          'delete_empty' => true,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\Goal'
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'appbundle_goal';
  }

}
