<?php
namespace Pyncer\Snyppet\Organization;

use Pyncer\Initializer;

Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER', null);
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_TOKEN_SCHEME', null);
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_TOKEN_REALM', null);
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_HEADER_ID', 'X-Organization');
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_HEADER_UID', 'X-Organization');
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_HEADER_ALIAS', 'X-Organization');
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_QUERY_PARAM_ID', 'organization_id');
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_QUERY_PARAM_UID', 'organization_uid');
Initializer::define('Pyncer\Snyppet\Organization\INITIALIZER_QUERY_PARAM_ALIAS', 'organization_alias');

Initializer::define('Pyncer\Snyppet\Organization\AUTO_INSERT', false);
