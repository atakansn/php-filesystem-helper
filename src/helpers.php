<?php


function move_directory(string $from, string $to, bool $overwrite = false): bool
{
    return (new \Helper\FileSystem())->moveDirectory($from,$to,$overwrite);
}

function delete_directory(string $directory, bool $preserve = false): bool
{
    return (new \Helper\FileSystem())->deleteDirectory($directory,$preserve);
}

function clean_directory(string $directory): bool
{
    return (new \Helper\FileSystem())->cleanDirectory($directory);
}

function copy_directory(string $directory, string $destination, int|null $options): bool
{
    return (new \Helper\FileSystem())->copyDirectory($directory,$destination,$options);
}

function __symlink(string $target, string $link)
{
    return (new \Helper\FileSystem())->symlink($target,$link);
}
