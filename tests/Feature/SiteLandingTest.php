<?php

it('renders the site landing page with member button', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('会員はこちら');
});

it('renders the member mypage', function () {
    $this->get('/mypage')
        ->assertOk()
        ->assertSee('ログイン')
        ->assertSee('新規登録');
});
