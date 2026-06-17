<?php
namespace Pyncer\Snyppet\Organization;

enum Initializer: string
{
    case TOKEN = 'token';
    case HEADER_ID = 'header_id';
    case HEADER_UID = 'header_uid';
    case HEADER_ALIAS = 'header_alias';
    case QUERY_PARAM_ID = 'query_param_id';
    case QUERY_PARAM_UID = 'query_param_uid';
    case QUERY_PARAM_ALIAS = 'query_param_alias';
}
