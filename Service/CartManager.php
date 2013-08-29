<?php

namespace Ecommerce\Bundle\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

use Ecommerce\Bundle\CoreBundle\Doctrine\Orm\CartRepository;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class CartManager
{
    protected $session;

    protected $storageKey;

    protected $cart;

    protected $cartRepository;


    /**
     * Constructor.
     */
    public function __construct(Session $session, CartRepository $cartRepository, $storageKey = '_ecommerce_cart_id')
    {
        $this->session        = $session;
        $this->cartRepository = $cartRepository;
        $this->storageKey     = $storageKey;
    }


    public function getCart()
    {
        if ($this->cart !== null) {
            return $this->cart;
        }

        if (null !== ($cartId = $this->getCartIdFromSession())) {
            $cart = $this->cartRepository->find($cartId);

            if ($cart && $cart->isExpired()) {
                $cart = null;
            }

            if ($cart) {
                $this->cart = $cart;
            }

            return $cart;
        }

        return null;
    }


    public function getOrCreateCart()
    {
        if (null !== ($cart = $this->getCart())) {
            return $cart;
        }

        $cart = $this->cartRepository->createNewCart();

        $this->session->set($this->storageKey, $cart->getId());

        $this->cart = $cart;

        return $cart;
    }


    private function getCartIdFromSession()
    {
        return $this->session->get($this->storageKey);
    }
}
