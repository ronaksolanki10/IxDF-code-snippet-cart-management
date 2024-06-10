<?php

namespace App\Http\Controllers\Api\User;

use App\Services\Cart\TransferGuestToLoggedInUserCart;
use App\Helpers\CartHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class AuthController
 *
 * Handles user authentication and registration.
 *
 * @package App\Http\Controllers\Api\User
 */
class AuthController extends Controller
{

    /**
     * AuthController constructor.
     *
     * @param TransferGuestToLoggedInUserCart $transferGuestToLoggedInUserCart
     */
    public function __construct(public TransferGuestToLoggedInUserCart $transferGuestToLoggedInUserCart)
    {
        
    }

    /**
     * Handle user login.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        // Consider at this stage, login is successful
        $this->transferGuestToLoggedInUserCart
            ->setLoggedInCart(auth()?->user()?->userId)
            ->setLoggedOutCart(CartHelper::getCookieValue())
            ->process();

        return response()->json(['message' => 'Login successful.']);
    }

    /**
     * Handle user registration.
     *
     * @param Register $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Register $request)
    {
        // Consider at this stage, registration is successful
        $this->transferGuestToLoggedInUserCart
            ->setLoggedInCart(auth()?->user()?->userId)
            ->setLoggedOutCart(CartHelper::getCookieValue())
            ->process();

        return response()->json(['message' => 'Registration successful.']);
    }
}