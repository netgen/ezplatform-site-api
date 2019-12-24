<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([])
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
        'no_alias_functions' => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_unset_on_property' => false,
        'php_unit_method_casing' => false,
        'php_unit_strict' => false,
        'php_unit_test_annotation' => false,
        'php_unit_test_case_static_method_calls' => false,
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_align' => ['align' => 'left'],
        'return_assignment' => false,
        'self_accessor' => false,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'space_after_semicolon' => false,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],

        // Additional rules
        'declare_strict_types' => true,
        'list_syntax' => ['syntax' => 'short'],
        'static_lambda' => true,
        'ternary_to_null_coalescing' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
