<?php

namespace Ecommerce\Bundle\CoreBundle\Model;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
interface ProductInterface
{
    public function getId();

    public function getType();

    public function getName();
}
