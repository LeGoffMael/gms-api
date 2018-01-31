<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * UserType short summary.
 *
 * UserType description.
 *
 * @version 1.0
 * @author Ma�l Le Goff
 */
class UserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name');
        $builder->add('email', EmailType::class);
        $builder->add('password');
        $builder->add('hashValidation');
        $builder->add('dateCreation', DateTimeType::class, array('widget' => 'single_text'));
        $builder->add('forgetPass');
        $builder->add('dateLastModification', DateTimeType::class, array('widget' => 'single_text'));
        $builder->add('role');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
            'csrf_protection' => false
        ]);
    }
}