<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RecurrType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $byMonthDay = $this->getByMonthDayChoices();
        $byMonth = $this->getByMonthChoices();
        $builder
            ->add('name')
            ->add('dateStart', DateTimeType::class, array(
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
            ->add('dateUntil', DateTimeType::class, array(
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
            ->add('count')
            ->add('interval')
            ->add('freq')
            ->add('byDay', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Sat' => 'SA',
                    'Sun' => 'SU',
                    'Mon' => 'MO',
                    'Tue' => 'TU',
                    'Wed' => 'WE',
                    'Thu' => 'TH',
                    'Fri' => 'FR'
                ],
                'label_attr' => ['class' => 'checkbox-inline']
            ])
            ->add('byMonthDay', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'CSV from -31 to 31',
                    'help' => 'CSV from -31 to 31'
                ]
            ])
            ->add('byYearDay', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'CSV from -366 to 366',
                    'help' => 'CSV from -366 to 366'
                ]
            ])
            ->add('byMonth', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $byMonth,
                'label_attr' => ['class' => 'checkbox-inline']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\RecurrEntity'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_recurr';
    }

    private function populateIntChoices($start, $end)
    {
        $res = [];
        for ($i = $start; $i <= $end; $i++) {
            $res[] = [$i => $i];
        }
        return $res;
    }

    private function getByMonthDayChoices()
    {
        return $this->populateIntChoices(-31, 31);
    }

    private function getByMonthChoices()
    {
        return $this->populateIntChoices(1, 12);
    }

    private function getByYearDayChoices()
    {
        return $this->populateIntChoices(-366, 366);
    }

}
