<?php

it('redirects unauthenticated users to login', function () {
    $response = test()->get('/');
    $response->assertRedirect('/login');
});
