<?php

namespace Ecommerce\Bundle\CoreBundle\Model;

use Ecommerce\Bundle\CoreBundle\Doctrine\Orm\CartItem;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
interface CartItemValidatorInterface
{
    public function isValid(CartItem $cartItem);
}
