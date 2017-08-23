<?php

$header = <<<EOT
Copyright 2017 Eric D. Hough (https://github.com/ehough)

This file is part of SpeedReader.

  https://github.com/ehough/speedreader

This Source Code Form is subject to the terms of the Mozilla Public
License, v. 2.0. If a copy of the MPL was not distributed with this
file, You can obtain one at http://mozilla.org/MPL/2.0/.
EOT;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->name('*.php');

return PhpCsFixer\Config::create()
    ->setRules(array(

        '@Symfony'                                  => true,

        'array_syntax'                              => array('syntax' => 'long'),
        'binary_operator_spaces'                    => array(
            'align_equals' => true,
            'align_double_arrow' => true),
        'braces'                                    => false,
        'class_keyword_remove'                      => true,
        'combine_consecutive_unsets'                => true,
        'concat_space'                              => array('spacing' => 'one'),
        'dir_constant'                              => true,
        'header_comment'                            => array(
            'header'   => $header,
            'separate' => 'bottom'),
        'linebreak_after_opening_tag'               => true,
        'mb_str_functions'                          => true,
        'no_extra_consecutive_blank_lines'          => false,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_unreachable_default_argument_value'     => true,
        'no_useless_else'                           => true,
        'no_useless_return'                         => true,
        'ordered_imports'                           => true,
        'php_unit_construct'                        => true,
        'php_unit_dedicate_assert'                  => true,
        'php_unit_fqcn_annotation'                  => true,
        'phpdoc_no_empty_return'                    => false,
        'phpdoc_order'                              => true,
        'pre_increment'                             => false,
        'protected_to_private'                      => true,
        'psr4'                                      => true,
        'semicolon_after_instruction'               => true,

    ))
    ->setFinder($finder);