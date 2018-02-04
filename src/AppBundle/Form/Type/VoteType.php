<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * VoteType short summary.
 *
 * VoteType description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class VoteType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('ip');
        $builder->add('user');
        $builder->add('image');
        $builder->add('value');
        $builder->add('date', DateTimeType::class, array('widget' => 'single_text'));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Vote',
            'csrf_protection' => false
        ]);
    }
}