<?php

namespace App\Filter;

use App\Classes\ProductFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProductFilterType extends AbstractType {

  

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('keywords', TextType::class, array( 'required' => false))
                ->add('min_price', NumberType::class, array(
                    'property_path' => 'minPrice',
                    'required' => false
                ))
                ->add('max_price', NumberType::class, array(
                    'property_path' => 'maxPrice',
                    'required' => false
                ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => \App\Classes\ProductFilter::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'productfilter';
    }

}