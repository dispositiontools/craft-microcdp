<?php

$datePattern = '<view:month|week|day>/<year:\d+>/<month:\d+>/<day:\d+>';

return [
  'micro-cdp'                                     => 'micro-cdp/cp/dashboard',
  'micro-cdp/records'                                     => 'micro-cdp/cp/records-index',
  'micro-cdp/records/add'                                 => 'micro-cdp/cp/records-entry-add',
  'micro-cdp/records/types'                               => 'micro-cdp/cp/recordtypes-index',
  'micro-cdp/records/types/add'                           => 'micro-cdp/cp/recordtypes-add',

  'micro-cdp/records/<recordId:\d+>'                     => 'micro-cdp/cp/records-entry-edit',

  'micro-cdp/records/types/<recordTypeId:\d+>/edit'       => 'micro-cdp/cp/recordtypes-edit',





];
