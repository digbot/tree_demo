<?php

namespace Digger\TreeDemoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('color', 'choice', array(
                        'expanded' => false,
                        'multiple' => false,
                        'required' => true,
                        'label' => 'Choose your car',
                        'choices' => \Digger\TreeDemoBundle\Entity\Category::getColorList(),
                        'empty_value' => 'Choose your color',
                        'empty_data'  => 0
                        ))
//            ->add('lft')
//            ->add('lvl')
//            ->add('rgt')
//            ->add('root')
//            ->add('parent')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Digger\TreeDemoBundle\Entity\Category'
        ));
    }

    public function getName()
    {
        return 'digger_treedemobundle_categorytype';
    }
}
