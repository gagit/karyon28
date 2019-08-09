<?php

/**
 * Description of FichaHiddenType
 *
 * @author Gabriel Toledo
 */

namespace BitLogic\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivoType extends AbstractType{
    


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'empty_value' => ' ',
            'empty_data' => null,
             'required' => false,
            'choices' => array(
                'S'=>'S',
                'N'=>'N',
            ),
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'activo';
    }
}
