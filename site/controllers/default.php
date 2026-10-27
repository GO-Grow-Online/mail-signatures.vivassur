<?php

return function ($kirby, $site, $pages, $page) {

    // On récupère toutes les signatures créées avec le blueprint "signature"
    $signatures = $site->pages()->template('signature');

    return [
        'signatures' => $signatures
    ];

};
