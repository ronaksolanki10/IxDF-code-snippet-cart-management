<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CartHelper;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Add an item to the cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min=1',
            // Add other necessary validations
        ]);

        $cart = CartHelper::get();

        // Add item to the cart
        $cart['items'][] = [
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
            // Add other necessary fields
        ];

        // Update the cart
        CartHelper::put($cart);

        return response()->json([
            'message' => 'Item added to cart successfully.',
            'cart' => $cart,
        ], 200);
    }

    /**
     * Delete an item from the cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item_id' => 'required|integer',
            // Add other necessary validations
        ]);

        $cart = CartHelper::get();

        // Remove item from the cart
        $cart['items'] = array_filter($cart['items'], function ($item) use ($data) {
            return $item['item_id'] !== $data['item_id'];
        });

        // Update the cart
        CartHelper::put($cart);

        return response()->json([
            'message' => 'Item removed from cart successfully.',
            'cart' => $cart,
        ], 200);
    }
}
