<?php

/**
 * @file
 * Nodeviewcount_views_data function.
 */

/**
 * Nodeviewcount_views_data function.
 */
function nodeviewcount_views_data() {
  $data = [];
  $data['nodeviewcount']['table']['group'] = t('Node count');
  $data['nodeviewcount']['table']['base'] = [
    'field' => 'nid',
    'title' => t('Node view count'),
    'help' => t('This table is containing the data about count view of node.'),
    'weight' => -10,
  ];
  $data['nodeviewcount']['table']['join'] = [
    'node' => [
      'handler' => 'views_join',
      'left_table' => 'node',
      'left_field' => 'nid',
      'field' => 'nid',
    ],
  ];
  $data['nodeviewcount']['nid'] = [
    'title' => t('Node id'),
    'help' => t('Node id.'),
    'relationship' => [
      'base' => 'node',
      'field' => 'nid',
      'handler' => 'views_handler_relationship',
      'label' => t('Node View Count Node'),
    ],
    'field' => [
      'handler' => 'views_handler_field_numeric',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'handler' => 'views_handler_filter_numeric',
    ],
    'sort' => [
      'handler' => 'views_handler_sort',
    ],
    'argument' => [
      'handler' => 'views_handler_argument_numeric',
    ],
  ];
  $data['nodeviewcount']['uid'] = [
    'title' => t('Who view'),
    'help' => t('Who view  of node.'),
    'relationship' => [
      'base' => 'users',
      'field' => 'uid',
      'handler' => 'views_handler_relationship',
      'label' => t('Node View Count User'),
    ],
    'field' => [
      'handler' => 'views_handler_field_numeric',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'handler' => 'views_handler_filter_numeric',
    ],
    'sort' => [
      'handler' => 'views_handler_sort',
    ],
    'argument' => [
      'handler' => 'views_handler_argument_numeric',
    ],
  ];
  $data['nodeviewcount']['timestamp'] = [
    'title' => t('Timestamp'),
    'help' => t('When user view of node.'),
    'field' => [
      'handler' => 'views_handler_field_date',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'handler' => 'views_handler_filter_date',
    ],
    'sort' => [
      'handler' => 'views_handler_sort_date',
    ],
  ];
  return $data;
}
