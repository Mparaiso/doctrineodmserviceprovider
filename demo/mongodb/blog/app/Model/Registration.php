<?php

namespace Model;

class Registration
{
    /**
     * @var \Document\User
     */
    protected $user;

    /**
     * @var Bool
     */
    protected $agreement;

    /**
     * @return \Document\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Document\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @param Bool $agreement
     */
    public function setAgreement($agreement)
    {
        $this->agreement = $agreement;
    }

    /**
     * @return Bool
     */
    public function getAgreement()
    {
        return $this->agreement;
    }
}
