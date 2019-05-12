<?php

$finder = PhpCsFixer\Finder::create()
    ->in('app')
    ->in('config');

return PhpCsFixer\Config::create()
    ->setHideProgress(true)
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@PSR2'        => true,
            'strict_param' => true,
            'array_syntax' => ['syntax' => 'short'],
            'binary_operator_spaces' => [
                'align_double_arrow' => true,
            ],
        ]
    )
    ->setFinder($finder);
