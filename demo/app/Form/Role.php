<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class Role extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->add('name')->add('role');
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return "post";
    }

}
