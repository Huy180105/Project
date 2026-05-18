<?php

it('renders the dashboard', function (): void {
    $this->get('/')->assertOk();
});

