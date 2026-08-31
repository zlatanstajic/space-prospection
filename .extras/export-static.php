<?php

declare(strict_types=1);

/**
 * Generate the read-only version of the site used by GitHub Pages.
 */

const SERVER_HOST = '127.0.0.1';
const DEFAULT_SERVER_PORT = '8099';

/**
 * Return an environment variable or its default when it is unset or empty.
 */
function environment_value(string $name, string $default): string
{
    $value = getenv($name);

    return $value === FALSE || $value === '' ? $default : $value;
}

/**
 * Normalize an absolute Unix path without requiring it to exist.
 */
function normalize_path(string $path): string
{
    if ($path === '' || $path[0] !== '/') {
        throw new RuntimeException('STATIC_OUTPUT_DIR must be an absolute path');
    }

    $parts = array();

    foreach (explode('/', $path) as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }

        if ($part === '..') {
            array_pop($parts);
            continue;
        }

        $parts[] = $part;
    }

    return '/' . implode('/', $parts);
}

/**
 * Remove a generated directory and everything below it.
 */
function remove_directory(string $directory): void
{
    if (is_link($directory)) {
        unlink($directory);
        return;
    }

    if (!is_dir($directory)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir() && !$item->isLink()) {
            rmdir($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }

    rmdir($directory);
}

/**
 * Refuse an output path whose existing directories redirect outside .build.
 */
function assert_safe_output_path(string $build_directory, string $output_directory): void
{
    if (is_link($build_directory)) {
        throw new RuntimeException('The .build directory must not be a symbolic link');
    }

    $relative_path = substr($output_directory, strlen($build_directory) + 1);
    $current_path = $build_directory;

    foreach (explode('/', $relative_path) as $part) {
        $current_path .= '/' . $part;

        if (is_link($current_path)) {
            throw new RuntimeException('STATIC_OUTPUT_DIR must not contain symbolic links');
        }
    }
}

/**
 * Copy a directory tree.
 */
function copy_directory(string $source, string $destination): void
{
    if (!is_dir($destination) && !mkdir($destination, 0777, TRUE) && !is_dir($destination)) {
        throw new RuntimeException('Unable to create directory: ' . $destination);
    }

    $iterator = new FilesystemIterator($source, FilesystemIterator::SKIP_DOTS);

    foreach ($iterator as $item) {
        $target = $destination . '/' . $item->getBasename();

        if ($item->isDir() && !$item->isLink()) {
            copy_directory($item->getPathname(), $target);
        } elseif (!copy($item->getPathname(), $target)) {
            throw new RuntimeException('Unable to copy file: ' . $item->getPathname());
        }
    }
}

/**
 * Fetch a page and require a successful HTTP response.
 */
function fetch_page(string $url): string
{
    $context = stream_context_create(array(
        'http' => array(
            'ignore_errors' => TRUE,
            'timeout' => 5,
        ),
    ));
    $contents = @file_get_contents($url, FALSE, $context);
    $status = isset($http_response_header[0]) ? $http_response_header[0] : '';

    if ($contents === FALSE || !preg_match('/^HTTP\/\S+ 2\d\d\b/', $status)) {
        throw new RuntimeException('Unable to fetch ' . $url . ($status === '' ? '' : ' (' . $status . ')'));
    }

    return $contents;
}

/**
 * Stop the local PHP server and close its process handle.
 *
 * @param resource|null $process
 */
function stop_server($process): void
{
    if (!is_resource($process)) {
        return;
    }

    $status = proc_get_status($process);

    if ($status['running']) {
        proc_terminate($process);
    }

    proc_close($process);
}

$root_directory = dirname(__DIR__);
$build_directory = $root_directory . '/.build';
$output_directory = normalize_path(
    environment_value('STATIC_OUTPUT_DIR', $build_directory . '/pages')
);
$base_path = environment_value('STATIC_BASE_PATH', '/space-prospection');
$server_port = environment_value('STATIC_EXPORT_PORT', DEFAULT_SERVER_PORT);
$server_url = 'http://' . SERVER_HOST . ':' . $server_port;
$server_log = $build_directory . '/static-export-server.log';
$process = NULL;
$log_handle = NULL;
$temporary_database = NULL;
$exit_code = 0;

try {
    if (strpos($output_directory . '/', $build_directory . '/') !== 0 || $output_directory === $build_directory) {
        throw new RuntimeException('STATIC_OUTPUT_DIR must be inside ' . $build_directory);
    }

    if (!preg_match('#^/[A-Za-z0-9._~/-]*$#', $base_path)) {
        throw new RuntimeException(
            'STATIC_BASE_PATH must be an absolute URL path without a query or fragment'
        );
    }

    if (!preg_match('/^[1-9][0-9]{0,4}$/', $server_port) || (int) $server_port > 65535) {
        throw new RuntimeException('STATIC_EXPORT_PORT must be a valid TCP port');
    }

    $base_path = rtrim($base_path, '/');
    $public_base_url = $base_path . '/';

    if (!is_dir($build_directory)
        && !mkdir($build_directory, 0777, TRUE)
        && !is_dir($build_directory)
    ) {
        throw new RuntimeException('Unable to create directory: ' . $build_directory);
    }

    assert_safe_output_path($build_directory, $output_directory);
    remove_directory($output_directory);

    if (!mkdir($output_directory, 0777, TRUE) && !is_dir($output_directory)) {
        throw new RuntimeException('Unable to create directory: ' . $output_directory);
    }

    $temporary_database = tempnam($build_directory, 'static-export-');

    if ($temporary_database === FALSE
        || !copy($root_directory . '/application/database/space-prospection.db', $temporary_database)
    ) {
        throw new RuntimeException('Unable to create the temporary static-export database');
    }

    $log_handle = fopen($server_log, 'wb');

    if ($log_handle === FALSE) {
        throw new RuntimeException('Unable to open server log: ' . $server_log);
    }

    $environment = getenv();
    $environment['APP_BASE_URL'] = $public_base_url;
    $environment['APP_DATABASE_PATH'] = $temporary_database;
    $environment['STATIC_EXPORT'] = '1';
    $environment['SKIP_COMPOSER_AUTOLOAD'] = '1';
    $environment['CI_ENV'] = 'production';
    $command = array(
        PHP_BINARY,
        '-d',
        'variables_order=EGPCS',
        '-S',
        SERVER_HOST . ':' . $server_port,
        '-t',
        $root_directory,
    );
    $descriptors = array(
        0 => array('file', '/dev/null', 'r'),
        1 => $log_handle,
        2 => $log_handle,
    );
    $process = proc_open($command, $descriptors, $pipes, $root_directory, $environment);

    if (!is_resource($process)) {
        throw new RuntimeException('Unable to start the local PHP server');
    }

    $ready = FALSE;

    for ($attempt = 0; $attempt < 20; $attempt++) {
        try {
            fetch_page($server_url . '/');
            $ready = TRUE;
            break;
        } catch (RuntimeException $exception) {
            $status = proc_get_status($process);

            if (!$status['running']) {
                break;
            }

            sleep(1);
        }
    }

    if (!$ready) {
        $log = file_get_contents($server_log);
        throw new RuntimeException(
            "The PHP server did not become ready:\n" . ($log === FALSE ? '' : $log)
        );
    }

    $routes = array('', 'about', 'projects', 'contact');
    $rendered_pages = array();

    foreach ($routes as $route) {
        $destination_directory = $route === ''
            ? $output_directory
            : $output_directory . '/' . $route;

        if (!is_dir($destination_directory)
            && !mkdir($destination_directory, 0777, TRUE)
            && !is_dir($destination_directory)
        ) {
            throw new RuntimeException('Unable to create directory: ' . $destination_directory);
        }

        $destination = $destination_directory . '/index.html';
        $html = fetch_page($server_url . '/' . $route);

        if (file_put_contents($destination, $html) === FALSE) {
            throw new RuntimeException('Unable to write page: ' . $destination);
        }

        $rendered_pages[] = $destination;
    }

    copy_directory($root_directory . '/assets', $output_directory . '/assets');

    if (touch($output_directory . '/.nojekyll') === FALSE) {
        throw new RuntimeException('Unable to create .nojekyll');
    }

    foreach ($rendered_pages as $page) {
        $html = file_get_contents($page);

        if ($html === FALSE || strtok($html, "\n") !== '<!DOCTYPE html>') {
            throw new RuntimeException('Static export did not produce a clean HTML document: ' . $page);
        }

        if (preg_match('/(<\?php|PHP Error|Deprecated:|localhost|127\.0\.0\.1)/', $html)) {
            throw new RuntimeException('Static export contains development output: ' . $page);
        }
    }

    $contact_page = file_get_contents($output_directory . '/contact/index.html');

    if ($contact_page === FALSE || preg_match('/(<form|contact\.js)/', $contact_page)) {
        throw new RuntimeException('Static contact page still contains server-dependent behavior');
    }

    echo 'Static site generated at ' . $output_directory . PHP_EOL;
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    $exit_code = 1;
} finally {
    stop_server($process);

    if (is_resource($log_handle)) {
        fclose($log_handle);
    }

    if (is_string($temporary_database) && is_file($temporary_database)) {
        unlink($temporary_database);
    }
}

exit($exit_code);
