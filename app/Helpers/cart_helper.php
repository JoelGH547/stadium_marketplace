<?php

if (! function_exists('cart_reset')) {
    function cart_reset(): void
    {
        session()->remove('cart');
    }
}

if (! function_exists('cart_set_booking')) {
    function cart_set_booking(array $data): void
    {
        session()->set('cart', $data);
    }
}

if (! function_exists('cart_get')) {
    function cart_get(): ?array
    {
        $cart = session()->get('cart');
        return is_array($cart) ? $cart : null;
    }
}
