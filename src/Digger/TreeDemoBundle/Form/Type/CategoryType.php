<?php

namespace Digger\TreeDemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Digger\TreeDemoBundle\Form\Type\EntityHiddenType;

use Symfony\Component\Form\FormInterface;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('error_bubbling' => true))
            ->add('color', 'choice', array(
                        'expanded' => false,
                        'multiple' => false,
                        'required' => true,
                        'label' => 'Choose your car',
                        'choices' => \Digger\TreeDemoBundle\Entity\Category::getColorList(),
                        'empty_value' => 'Choose your color',
                        'empty_data'  => 0
                        ))
              ->add('parent', 'entity', array(
                            'class'=> 'Digger\TreeDemoBundle\Entity\Category', 
                            'property' => 'title',
                            'read_only' => true
              ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Digger\TreeDemoBundle\Entity\Category',
        ));
    }

    public function getName()
    {
        return 'digger_treedemobundle_categorytype';
    }
}
