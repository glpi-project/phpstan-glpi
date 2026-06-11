<?php

// --- will be caught - reported in test ---

    // harcoded strings
    Session::checkRight('hardcoded', READ);
    Session::checkRightsOr('reservation', [CREATE, UPDATE, DELETE, PURGE]);
    Session::haveRight('change', UPDATE);
    Session::haveRightsOr('ticketvalidation', TicketValidation::getValidateRights());
    Session::haveRightsAnd('reservation', [CREATE, UPDATE, DELETE, PURGE]);

    // wrong constant / static property
    Session::checkRight(Ticket::MATRIX_FIELD, READ); // constant
    Session::checkRight(\Glpi\Api::$api_url, READ); // static variable but wrong one

    // fully qualified Session class is equivalent
    \Session::checkRight('ticket', READ);

    // named argument for 'module'
    Session::checkRight(right: READ, module: 'config');
    Session::checkRight(module: 'config', right: READ);


// --- will be caught - variable holding a constant string ---

    $variable = 'logs';
    Session::checkRight($variable, UPDATE); // reported: PHPStan infers ConstantStringType

// --- won't be caught - correct usage ---

Session::checkRight(Ticket::$rightname, READ);
Session::checkRightsOr(\Glpi\Socket::$rightname, [CREATE, UPDATE, DELETE, PURGE]);
Session::haveRight(Ticket::$rightname, UPDATE);
Session::haveRightsOr(Ticket::$rightname, TicketValidation::getValidateRights());
Session::haveRightsAnd(Ticket::$rightname, [CREATE, UPDATE, DELETE, PURGE]);

// --- won't be caught - not applicable ---

Foo::checkRight('not_session', READ); // not the Session class
Session::login('not_right_method', READ); // not in CHECKED_METHODS
Session::checkRight(...$args); // variadic — no positional 'module' arg

