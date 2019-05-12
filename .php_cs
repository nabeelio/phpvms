<?php

$finder = PhpCsFixer\Finder::create()
    ->in('app')
    ->in('config');

return PhpCsFixer\Config::create()
    ->setHideProgress(true)
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2'                             => true,
        'strict_param'                      => true,
        'no_php4_constructor'               => true,
        'no_extra_blank_lines'              => true,
        'no_superfluous_elseif'             => true,
        'single_line_comment_style'         => false,
        'simple_to_complex_string_variable' => true,
        'array_syntax'                      => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces'            => [
            'align_double_arrow' => true,
        ],
        /*
        'blank_line_before_statement'       => [
            'statements' => [
                'declare',
                'for',
                'return',
                'throw',
                'try',
            ],
        ],
        */
    ])
    ->setFinder($finder);
