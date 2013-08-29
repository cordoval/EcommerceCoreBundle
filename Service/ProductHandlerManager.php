<?php

namespace Ecommerce\Bundle\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Ecommerce\Bundle\CoreBundle\Model\ProductHandlerInterface;
use Ecommerce\Bundle\CoreBundle\Model\ProductInterface;
use Ecommerce\Bundle\CoreBundle\Model\CartItemValidatorInterface;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class ProductHandlerManager
{
    private $productHandlers;
    private $eventDispatcher;


    /**
     * Constructor.
     *
     * @param ProductHandlerInterface[] $productHandlers
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $productHandlers)
    {
        if (!$productHandlers) {
            throw new \InvalidArgumentException('You must at least add one authentication provider.');
        }

        $this->productHandlers = $productHandlers;
    }


    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }


    public function getProductHandler(ProductInterface $product)
    {
        $lastException = null;
        $result = null;

        foreach ($this->productHandlers as $productHandler) {
            if (!$productHandler->supports($product)) {
                continue;
            }

            return $productHandler;
        }

        throw new \Exception(sprintf('No product handler found for product "%s".', $product->getId()));

    }


    public function resolveCartItem(ProductInterface $product, Request $request)
    {
        $lastException = null;
        $result = null;

        foreach ($this->productHandlers as $productHandler) {
            if (!$productHandler->supports($product)) {
                continue;
            }

            try {
                $cartHandler = $productHandler->getCartHandler($product);
                $result = $cartHandler->processRequest($product, $request);

                if (null !== $result) {
                    break;
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        if (null !== $result) {
            if (null !== $this->eventDispatcher) {
                $this->eventDispatcher->dispatch(EcommerceEvents::CART_ITEM_RESOLVED, new CartEvent($result));
            }

            $cartHandler = $productHandler->getCartItemValidator($result);
            if ($cartHandler instanceof CartItemValidatorInterface && $cartHandler->isValid($result) !== true) {
                throw new \Exception('Cart item was not valid');
            }

            return $result;
        }

        if (null === $lastException) {
            $lastException = new \Exception(sprintf('No Authentication Provider found for token of class "%s".', get_class($product)));
//            $lastException = new ProviderNotFoundException(sprintf('No Authentication Provider found for token of class "%s".', get_class($product)));
        }

//        if (null !== $this->eventDispatcher) {
//            $this->eventDispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_FAILURE, new AuthenticationFailureEvent($token, $lastException));
//        }

//        $lastException->setProduct($product);

        throw $lastException;
    }
}
