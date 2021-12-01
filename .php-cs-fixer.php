<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$header = <<<'EOF'
This file is part of PHP CS Fixer.
(c) Fabien Potencier <fabien@symfony.com>
    Dariusz Rumiński <dariusz.ruminski@gmail.com>
This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->ignoreVCSIgnored(true)
    ->exclude('tests/data')
    ->exclude('storage')
    ->exclude('resources')
    ->in(__DIR__)
    ->append([
        __DIR__.'/dev-tools/doc.php',
        // __DIR__.'/php-cs-fixer', disabled, as we want to be able to run bootstrap file even on lower PHP version, to show nice message
        __FILE__,
    ])
;

$config = new PhpCsFixer\Config();
$config
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
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal'
            ]
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

return $config;
