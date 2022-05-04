<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Output;
use App\Entity\Place;
use App\Entity\State;
use App\Entity\User;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class OutputType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startdatetime', \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class, [
                'required' => true,
                ])
            ->add('duration')
            ->add('registrationdeadline')
            ->add('registrationmaxnumber')
            ->add('outputinfos')
            ->add('place',EntityType::class, [
                'class'=>Place::class,
                'choice_label'=>'name',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Output::class,
        ]);
    }
}
