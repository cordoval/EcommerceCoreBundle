<?php

namespace Ecommerce\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

use Ecommerce\Bundle\CoreBundle\Doctrine\Orm\Cart;
use Ecommerce\Bundle\CoreBundle\Doctrine\Orm\CartItem;

class CartController extends Controller
{
    public function indexAction(Request $request)
    {
        $cartManager = $this->get('ecommerce.cart.manager');
        $cart        = $cartManager->getCart();

        // @TODO: Cart items validation

        return $this->render(
            'EcommerceCoreBundle:Cart:index.html.twig',
            array(
                'cart' => $cart,
            )
        );
    }


    public function addProductAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            throw new \Exception('Wrong request method');
        }

        if (null === ($productId = $request->request->get('product_id'))) {
            throw new \Exception('No product id provided');
        }

        $cartManager = $this->get('ecommerce.cart.manager');
        /** @var Cart $cart */
        $cart = $cartManager->getOrCreateCart();

        $em = $this->getDoctrine()->getManager();

        $productEntity = $em->getRepository('EcommerceAppBundle:Product')->find($productId);


        $productHandlerManager = $this->get('ecommerce.product.handler_manager');

        // @TODO: event cart pre add item

        try {
            $cartItem = $productHandlerManager->resolveCartItem($productEntity, $request);

            if ($cartItem instanceof CartItem) {

                $cartItem->setCart($cart);
                $em->persist($cartItem);

                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'success',
                    sprintf('%s was successfully added to your cart', $cartItem->getProduct()->getName())
                );
            }
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        }

        // @TODO: event cart post add item

        return $this->redirect($this->generateUrl('ecommerce_cart'));
    }


    // @TODO: updateCartItemAction PUT method


    public function removeCartItemAction(Request $request, $cartItemId)
    {
//        if (null === ($productId = $request->request->get('product_id'))) {
//            throw new \Exception('No product id provided');
//        }

        $cartManager = $this->get('ecommerce.cart.manager');
        /** @var Cart $cart */
        $cart = $cartManager->getOrCreateCart();

        $cartItems = $cart->getItems();

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("id", $cartItemId));

        $foundItems = $cartItems->matching($criteria);

        if (count($foundItems) === 0) {
        } elseif (count($foundItems) > 1) {
            // throw exception?
        }

        if (!$request->isMethod('DELETE')) {
            throw new \Exception('Wrong request method');
        }

        $cartItem = $foundItems->first();


        // @TODO: event cart pre remove item

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $em->remove($cartItem);
        $em->flush();


        $request->getSession()->getFlashBag()->add(
            'success',
            sprintf('%s was successfully removed from your cart', $cartItem->getProduct()->getName())
        );


        return $this->redirect($this->generateUrl('ecommerce_cart'));
    }
}
