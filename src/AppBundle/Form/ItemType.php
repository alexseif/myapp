<?php

namespace AppBundle\Form;

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
        ->add('priority')
        ->add('sort')
        ->add('type', ChoiceType::class, [
          'choices' => [
            new Type('Type1'),
            new Type('Type2'),
            new Type('Type3'),
            new Type('Type4'),
          ],
          // "name" is a property path, meaning Symfony will look for a public
// property or a public method like "getName()" to define the input
// string value that will be submitted by the form
          'choice_value' => 'name',
          // a callback to return the label for a given choice
// if a placeholder is used, its empty value (null) may be passed but
// its label is defined by its own "placeholder" option
          'choice_label' => function(?Type $type) {
            return $type ? strtoupper($type->getName()) : '';
          },
          // returns the html attributes for each option input (may be radio/checkbox)
          'choice_attr' => function(?Type $type) {
            return $type ? ['class' => 'type_' . strtolower($type->getName())] : [];
          },
          // every option can use a string property path or any callable that get
// passed each choice as argument, but it may not be needed
    ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Item::class,
    ]);
  }

}
