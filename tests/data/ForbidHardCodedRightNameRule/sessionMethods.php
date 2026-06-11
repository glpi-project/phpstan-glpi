<?php

// --- will be caught - reported in test ---

    // harcoded strings
    Session::checkRight('hardcoded', READ);
    Session::checkRightsOr('reservation', [CREATE, UPDATE, DELETE, PURGE]);
    Session::haveRight('change', UPDATE);
    Session::haveRightsOr('ticketvalidation', TicketValidation::getValidateRights());
    Session::haveRightsAnd('reservation', [CREATE, UPDATE, DELETE, PURGE]);

    // wrong variable
    Session::checkRight(Ticket::MATRIX_FIELD, READ); // constant
    Session::checkRight(\Glpi\Api::$api_url, READ); // static variable but wrong one


// --- won't be caught but should ---

    // phpstan cannot analyse this, it seems, but should be caught - @todo check if possible
    $variable = 'logs';
    Session::checkRight($variable, UPDATE);

// --- won't be caught - correct usage ---

Session::checkRight(Ticket::$rightname, READ);
Session::checkRightsOr(\Glpi\Socket::$rightname, [CREATE, UPDATE, DELETE, PURGE]);
Session::haveRight(Ticket::$rightname, UPDATE);
Session::haveRightsOr(Ticket::$rightname, TicketValidation::getValidateRights());
Session::haveRightsAnd(Ticket::$rightname, [CREATE, UPDATE, DELETE, PURGE]);
