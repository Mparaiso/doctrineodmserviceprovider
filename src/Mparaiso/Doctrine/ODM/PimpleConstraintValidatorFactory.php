<?php

/**
 *
 * @author M.Paraiso
 *
 */

namespace Mparaiso\Doctrine\ODM;

use Pimple;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

/**
 * Uses a service container to create constraint validators.
 *
 */
class PimpleConstraintValidatorFactory implements ConstraintValidatorFactoryInterface {

    protected $container;
    protected $validators;

    /**
     * Constructor.
     *
     * @param Pimple $container  The service container
     * @param array              $validators An array of validators
     */
    public function __construct(Pimple $container, array $validators = array()) {
        $this->container = $container;
        $this->validators = $validators;
    }

    /**
     * Returns the validator for the supplied constraint.
     *
     * @param Constraint $constraint A constraint
     *
     * @return Symfony\Component\Validator\ConstraintValidator A validator for the supplied constraint
     */
    public function getInstance(Constraint $constraint) {

        $className = $constraint->validatedBy();

        if (!isset($this->validators[$className])) {
            $this->validators[$className] = new $className();
        } elseif (is_string($this->validators[$className])) {
            $this->validators[$className] = $this->container[$this->validators[$className]];
        }

        return $this->validators[$className];
    }
    
    /* GETTER ajouter des validators après l'instanciation */
    public function getValidators() {
        return $this->validators;
    }

}
