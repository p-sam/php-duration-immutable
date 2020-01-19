<?php

declare(strict_types=1);

$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PHP73Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        'backtick_to_shell_exec' => true,
        'date_time_immutable' => true,
        'declare_strict_types' => true,
        'final_public_method_for_abstract_class' => true,
        'final_static_access' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'expectedException',
                'expectedExceptionMessage',
                'expectedExceptionMessageRegExp',
            ],
        ],
        'global_namespace_import' => true,
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_interfaces' => true,
        'phpdoc_line_span' => true,
        'phpdoc_to_param_type' => true,
        'phpdoc_to_return_type' => true,
        'single_line_comment_style' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/src')
            ->append([__FILE__])
    )
;

return $config;
