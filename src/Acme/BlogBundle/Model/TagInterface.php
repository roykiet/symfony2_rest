<?php

namespace Acme\BlogBundle\Model;

Interface TagInterface
{
    /**
     * Set name
     *
     * @param string $name
     * @return TagInterface
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string 
     */
    public function getName();

}
