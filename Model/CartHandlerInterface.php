<?php

namespace Ecommerce\Bundle\CoreBundle\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
interface CartHandlerInterface
{
    public function processRequest(ProductInterface $product, Request $request);
}
