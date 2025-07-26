<?php

test('the application returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $this->get('/')
        ->assertStatus(200);
});
