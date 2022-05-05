<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Output;
use App\Entity\Place;
use App\Entity\State;
use App\Entity\User;
use Doctrine\DBAL\Types\DateTimeType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutputType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startdatetime', \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                ])
            ->add('duration',)
            ->add('registrationdeadline', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                ])
            ->add('registrationmaxnumber')
            ->add('outputinfos')
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'required' => true,
                'mapped' => false])
            ->add('place', EntityType::class, [
                        'class' => Place::class,
                        'choice_label' => 'name',
                        'required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Output::class,
        ]);
    }
}
