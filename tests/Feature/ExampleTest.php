<?php

it('bloquea las solicitudes excesivas de códigos', function () {
    RateLimiter::clear('127.0.0.1');

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('index.store'), [
            'email' => 'prueba@example.com',
        ]);
    }

    $this->post(route('index.store'), [
        'email' => 'prueba@example.com',
    ])->assertTooManyRequests();
});