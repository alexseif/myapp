<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManagementSearchType extends AbstractType
{

    public function buildForm(
      FormBuilderInterface $builder,
      array $options
    ): void {
        $builder
          ->add('search', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'csrf_protection' => false,
        ]);
    }

}
