<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/hubspot/webhook',
        '/hubspot/add-association',
        '/hubspot/search',
        '/hubspot/custom',
        '/hubspot/token',
        '/template',
        '/template-token',
        '/event-create',
        '/note-create',
        '/hubspot/cards',
        '/hubspot/fetch-cards',
        '/call-create',
        '/call-update',
        'call-delete',
        'call-association',
        'list-create',
        'create-file'
    ];
}
