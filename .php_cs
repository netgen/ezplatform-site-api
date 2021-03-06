<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['var', 'vendor'])
    ->files()->name('*.php')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,

        // Overrides for rules included in PhpCsFixer rule sets
        'array_syntax' => ['syntax' => 'short'],
        'cast_spaces' => ['space' => 'single'],
        'concat_space' => ['spacing' => 'one'],
        'method_chaining_indentation' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'native_function_invocation' => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_unset_on_property' => false,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'php_unit_internal_class' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'php_unit_test_class_requires_covers' => false,
        'php_unit_method_casing' => false,
        'php_unit_strict' => false,
        'php_unit_test_annotation' => false,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'return_assignment' => false,
        'self_accessor' => false,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'space_after_semicolon' => false,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],

        // Additional rules
        'date_time_immutable' => true,
        'global_namespace_import' => [
            'import_classes' => null,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'declare_strict_types' => true,
        'list_syntax' => ['syntax' => 'short'],
        'static_lambda' => true,
        'ternary_to_null_coalescing' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'native_constant_invocation' => true,
        'mb_str_functions' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
