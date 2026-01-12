<?php

declare(strict_types=1);

use App\Livewire\Invoice;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Invoice::class)
        ->assertStatus(200);
});
