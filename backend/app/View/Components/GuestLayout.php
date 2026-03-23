<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Apunta al layout guest que nosotros definimos.
     * Breeze genera este componente buscando layouts.guest
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
