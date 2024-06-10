<?php

namespace App\Services;

use App\Helpers\CartHelper;
use Illuminate\Support\Facades\Auth;

class CartService
{
    private $loggedInCart;
    private $loggedOutCart;

    /**
     * Get the logged-in user's cart.
     *
     * @return array
     */
    public function getLoggedInCart(): array
    {
        return $this->loggedInCart;
    }

    /**
     * Set the logged-in user's cart.
     */
    private function setLoggedInCart(string $userId): void
    {
        $userId = $userId;
        $this->loggedInCart = CartHelper::fetchCartDetails($userId);
    }

    /**
     * Get the logged-out user's cart.
     *
     * @return array
     */
    public function getLoggedOutCart(): array
    {
        return $this->loggedOutCart;
    }

    /**
     * Set the logged-out user's cart.
     */
    private function setLoggedOutCart(string $cookieValue): void
    {
        $cartId = $cookieValue;
        $this->loggedOutCart = CartHelper::fetchCartDetails($cartId);
    }

    /**
     * Merge the logged-out user's cart with the logged-in user's cart.
     *
     * @return array
     */
    public function mergeCarts(): array
    {
        $loggedInCart = $this->getLoggedInCart();
        $loggedOutCart = $this->getLoggedOutCart();

        foreach ($loggedOutCart['items'] as $loggedOutItem) {
            $exists = false;

            foreach ($loggedInCart['items'] as &$loggedInItem) {
                if ($loggedInItem['item_id'] == $loggedOutItem['item_id']) {
                    $loggedInItem['quantity'] += $loggedOutItem['quantity'];
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $loggedInCart['items'][] = $loggedOutItem;
            }
        }

        return $loggedInCart;
    }

    /**
     * Save the merged cart for the logged-in user.
     */
    public function process(): void
    {
        CartHelper::put($this->mergeCarts());
    }
}
