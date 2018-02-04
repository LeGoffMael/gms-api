<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SettingsType short summary.
 *
 * SettingsType description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class SettingsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id');
        $builder->add('title');
        $builder->add('limitGallery');
        $builder->add('theme');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Settings',
            'csrf_protection' => false
        ]);
    }
}