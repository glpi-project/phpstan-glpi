<?php

// --- will be caught - reported in test ---

    // harcoded strings
    Session::checkRight('hardcoded', READ);
    Session::checkRightsOr('reservation', [CREATE, UPDATE, DELETE, PURGE]);
    Session::haveRight('change', UPDATE);
    Session::haveRightsOr('ticketvalidation', TicketValidation::getValidateRights());
    Session::haveRightsAnd('reservation', [CREATE, UPDATE, DELETE, PURGE]);

    // fully qualified Session class is equivalent
    \Session::checkRight('ticket', READ);

    // named argument for 'module'
    Session::checkRight(right: READ, module: 'config');
    Session::checkRight(module: 'config', right: READ);

// --- won't be caught

    // correct usage

    Session::checkRight(Ticket::$rightname, READ);
    Session::checkRightsOr(\Glpi\Socket::$rightname, [CREATE, UPDATE, DELETE, PURGE]);
    Session::haveRight(Ticket::$rightname, UPDATE);
    Session::haveRightsOr(Ticket::$rightname, TicketValidation::getValidateRights());
    Session::haveRightsAnd(Ticket::$rightname, [CREATE, UPDATE, DELETE, PURGE]);

    // not applicable

    // wrong constant / static property — not caught, rule only checks literal strings
    Session::checkRight(Ticket::MATRIX_FIELD, READ);
    Session::checkRight(\Glpi\Api::$api_url, READ);

    // intermediate variable — not caught, rule does not resolve variable types
    $variable = 'logs';
    Session::checkRight($variable, UPDATE);

    Foo::checkRight('not_session', READ); // not the Session class
    Session::login('not_right_method', READ); // not in CHECKED_METHODS
    Session::checkRight(...$args); // variadic — no positional 'module' arg

