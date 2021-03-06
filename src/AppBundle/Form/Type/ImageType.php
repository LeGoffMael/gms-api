<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * ImageType short summary.
 *
 * ImageType description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class ImageType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {      
        $builder->add('url');
        $builder->add('createdAt', DateTimeType::class, array('widget' => 'single_text'));
        $builder->add('updatedAt', DateTimeType::class, array('widget' => 'single_text'));
        $builder->add('description');
        $builder->add('creator');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Image',
            'csrf_protection' => false
        ]);
    }
}