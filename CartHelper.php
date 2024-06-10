<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Cart;

class CartHelper
{
    public const CACHE_PREFIX = 'cart_';
    public const COOKIE_NAME = 'packt_cart';
    public const CACHE_EXPIRY_TIME_IN_SECS = 3600;

    /**
     * Get the current user's cart details.
     *
     * @return array
     */
    public static function get(): array
    {
        return self::fetchCartDetails(self::getCartId());
    }

    /**
     * Store or update the cart data.
     *
     * @param array $data
     * @return bool
     */
    public static function put(array $data): bool
    {
        $cartId = self::getCartId();
        $cacheId = self::CACHE_PREFIX . $cartId;

        if (!isset($data['items']) || is_null($data['items'])) {
            // All items from the cart have been deleted, so remove details from cache and database
            self::flush($cartId);
            return true;
        }

        RedisCacheHelper::set($cacheId, json_encode($data), self::CACHE_EXPIRY_TIME_IN_SECS);
        $dataToBeSaved = [
            'data' => json_encode($data),
            'expires_at' => Carbon::now()->addSeconds(self::CACHE_EXPIRY_TIME_IN_SECS)
        ];

        $cartModel = new Cart();
        $cartModel->updateOrCreate(['cart_id' => $cartId], $dataToBeSaved);

        return true;
    }

    /**
     * Flush the cart details from both cache and database.
     *
     * @param string $cartId
     * @return void
     */
    public static function flush(string $cartId): void
    {
        $cacheId = self::CACHE_PREFIX . $cartId;
        RedisCacheHelper::delete($cacheId);

        $cartModel = new Cart();
        $cartModel->deleteByCartId($cartId);
    }

    /**
     * Fetch cart details from cache or database.
     *
     * @param string $cartId
     * @return array
     */
    private static function fetchCartDetails(string $cartId): array
    {
        $cacheId = self::CACHE_PREFIX . $cartId;
        $cacheData = RedisCacheHelper::get($cacheId);

        if ($cacheData) {
            return json_decode($cacheData, true);
        }

        // Fetch from database
        $cartModel = new Cart();
        $data = $cartModel->getById($cartId);

        if (empty($data)) {
            return self::getDefaultCart();
        }

        if (strtotime($data['expires_at']) < time()) {
            return self::getDefaultCart();
        }

        RedisCacheHelper::set($cacheId, json_encode($data), self::CACHE_EXPIRY_TIME_IN_SECS);

        return json_decode($data['data'], true);
    }

    /**
     * Get the current cart ID.
     *
     * @return string
     */
    private static function getCartId(): string
    {
        return auth()->user()->userId ?? self::getCookieValue();
    }

    /**
     * Get the value of the cart cookie, or generate a new one if it doesn't exist.
     *
     * @return string
     */
    private static function getCookieValue(): string
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            return $_COOKIE[self::COOKIE_NAME];
        }

        $uniqueString = Str::random(30);
        setcookie(self::COOKIE_NAME, $uniqueString, time() + (86400 * 30), "/");

        return $uniqueString;
    }

    /**
     * Get the default cart structure.
     *
     * @return array
     */
    private static function getDefaultCart(): array
    {
        return [
            'items' => [],
            'total' => 0,
        ];
    }
}