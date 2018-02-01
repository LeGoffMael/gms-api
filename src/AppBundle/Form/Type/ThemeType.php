<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ThemeType short summary.
 *
 * ThemeType description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class ThemeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name');
        $builder->add('mainColor');
        $builder->add('mainDarkFontColor');
        $builder->add('bodyColor');
        $builder->add('bodyFontColor');
        $builder->add('sideBarColor');
        $builder->add('sideBarFontColor');
        $builder->add('linkColor');
        $builder->add('linkHoverColor');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Theme',
            'csrf_protection' => false
        ]);
    }
}