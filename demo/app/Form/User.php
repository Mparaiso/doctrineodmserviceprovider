<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class User extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->add('username')->add('email')->add('userRoles',null,array('label'=>"Roles"));
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return "post";
    }

}
