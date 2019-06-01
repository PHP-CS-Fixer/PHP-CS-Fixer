#!/usr/bin/env php
<?php

require_once __DIR__.'/../vendor/autoload.php';

const COMPOSER_JSON_FILE = __DIR__.'/../composer.json';

/**
 * @param string $version
 *
 * @return string
 */
function convertAppVersionToAliasedVersion($version)
{
    $parts = explode('.', $version, 3);

    return sprintf('%d.%d-dev', $parts[0], $parts[1]);
}

$composerJson = json_decode(file_get_contents(COMPOSER_JSON_FILE), true);

if (isset($composerJson['extra']['branch-alias'])) {
    $composerJson['extra']['branch-alias'] = [
        'dev-master' => convertAppVersionToAliasedVersion(PhpCsFixer\Console\Application::VERSION),
    ];
}

file_put_contents(COMPOSER_JSON_FILE, json_encode($composerJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");
