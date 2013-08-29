<?php

namespace Ecommerce\Bundle\CoreBundle\Model;

use Ecommerce\Bundle\CoreBundle\Doctrine\Orm\CartItem;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
interface ProductHandlerInterface
{
    public function supports(ProductInterface $product);


    /**
     * @param ProductInterface $product
     * @return CartHandlerInterface
     */
    public function getCartHandler(ProductInterface $product);

    /**
     * @param CartItem $cartItem
     * @return CartItemValidatorInterface
     */
    public function getCartItemValidator(CartItem $cartItem);
}
