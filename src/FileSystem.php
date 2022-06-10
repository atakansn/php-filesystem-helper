<?php

namespace Helper;

use RuntimeException;

class FileSystem
{

    /**
     * @param string $name
     * @return string
     */
    public function basename(string $name): string
    {
        return pathinfo($name, PATHINFO_BASENAME);
    }

    /**
     * @param string $name
     * @return array|string|string[]
     */
    public function dirname(string $name)
    {
        return pathinfo($name, PATHINFO_DIRNAME);
    }

    /**
     * @param string $name
     * @return array|string|string[]
     */
    public function extension(string $name)
    {
        return pathinfo($name, PATHINFO_EXTENSION);
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function exists(string $fileName): bool
    {
        return file_exists($fileName);
    }

    /**
     * @param string $path
     * @param string $target
     * @return bool
     */
    public function move(string $path, string $target): bool
    {
        return rename($path, $target);
    }

    /**
     * @param string $path
     * @param string $target
     * @return bool
     */
    public function copy(string $path, string $target): bool
    {
        return copy($path, $target);
    }

    /**
     * @param string $target
     * @param string $link
     * @return bool|void
     */
    public function symlink(string $target, string $link)
    {
        if (!(PHP_OS_FAMILY === 'Windows')) {
            return symlink($target, $link);
        }

        $mode = $this->isDirectory($target) ? 'J' : 'H';

        exec("mklink /{$mode} " . escapeshellarg($link) . ' ' . escapeshellarg($target));
    }

    /**
     * @param string $path
     * @param int|null $options
     * @return array|string
     */
    public function info(string $path, int $options = null): array|string
    {
        return pathinfo($path, $options ?? PATHINFO_ALL);
    }

    /**
     * @param string $path
     * @return string
     */
    public function type(string $path): string
    {
        return filetype($path);
    }

    /**
     * @param string $name
     * @return int
     */
    public function size(string $name): int
    {
        return filesize($name);
    }

    /**
     * @param string $name
     * @return int
     */
    public function lastModified(string $name): int
    {
        return filemtime($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isDirectory(string $name)
    {
        return is_dir($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isReadable(string $name): bool
    {
        return is_readable($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isWritible(string $name): bool
    {
        return is_writable($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isFile(string $name): bool
    {
        return is_file($name);
    }

    /**
     * @param string $pattern
     * @param int $flag
     * @return array|false
     */
    public function glob(string $pattern, int $flag = 0): array|false
    {
        return glob($pattern, $flag);
    }

    /**
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function directoryExists(string $path, int $mode = 0755, bool $recursive = true)
    {
        if (!$this->isDirectory($path)) {
            $this->makeDirectory($path, $mode, $recursive);
        }

        return false;
    }

    /**
     * @param string $from
     * @param string $to
     * @param bool $overwrite
     * @return bool
     */
    public function moveDirectory(string $from, string $to, bool $overwrite = false): bool
    {
        if ($overwrite && $this->isDirectory($to) && !$this->deleteDirectory($to)) {
            return false;
        }

        return @rename($from, $to) === true;
    }

    /**
     * @param string $directory
     * @param bool $preserve
     * @return bool
     */
    public function deleteDirectory(string $directory, bool $preserve = false): bool
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }

        $items = new \FilesystemIterator($directory);

        foreach ($items as $item) {
            if ($item->isDir() && !$item->isLink()) {
                $this->deleteDirectory($item->getPathname());
            } else {
                $this->delete($item->getPathname());
            }
        }

        if (!$preserve) {
            @rmdir($directory);
        }

        return true;
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function deleteDirectories(string $directory)
    {
        $allDirectories = $this->directories($directory);

        if (!empty($allDirectories)) {
            foreach ($allDirectories as $directoryName) {
                $this->deleteDirectory($directoryName);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $directory
     * @param bool $hidden
     * @return array
     */
    public function directories(string $directory, bool $hidden = false)
    {
        $directories = [];

        $files = new \FilesystemIterator($directory);

        foreach ($files as $file) {
            $directories[] = $file->getPathname();
        }

        return $directories;


    }


    /**
     * @param string $directory
     * @param string $destination
     * @param int|null $options
     * @return bool
     */
    public function copyDirectory(string $directory, string $destination, int|null $options): bool
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }

        $options = $options ?: \FilesystemIterator::SKIP_DOTS;

        $this->directoryExists($destination, 0777);

        $items = new \FilesystemIterator($destination, $options);

        foreach ($items as $item) {
            $target = $destination . '/' . $item->getBasename();

            if ($item->isDir()) {
                $path = $item->getPathname();

                if (!$this->copyDirectory($path, $target, $options)) {
                    return false;
                }
            }

            if (!$this->copy($item->getPathname(), $target)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function cleanDirectory(string $directory): bool
    {
        return $this->deleteDirectory($directory, true);
    }

    /**
     * @param string|array $paths
     * @return bool
     */
    public function delete(string|array $paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (@unlink($path)) {
                    clearstatcache(false, $path);
                } else {
                    $success = false;
                }
            } catch (\ErrorException $exception) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @param bool $force
     * @return bool
     */
    public function makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }

        return mkdir($path, $mode, $recursive);
    }

    /**
     * @param string $file
     * @return string|bool
     */
    public function mimeType(string $file): string|bool
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
    }


    /**
     * @param string $path
     * @param string $contents
     * @param bool $lock
     * @return false|int
     */
    public function filePut(string $path, string $contents, bool $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * @param string $path
     * @param string $content
     * @return bool
     */
    public function replace(string $path, string $content) : bool
    {
        clearstatcache(true, $path);

        $file = realpath($path) ?: $path;

        $tempPath = tempnam($this->dirname($file), $this->basename($file));

        chmod($tempPath, 0777 - umask());

        $this->filePut($tempPath, $content);

        return rename($tempPath, $file);
    }

    /**
     * @param array|string $search
     * @param array|string $replace
     * @param string $path
     * @return bool|int
     */
    public function replaceInFile(array|string $search, array|string $replace, string $path)
    {
        return file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }


    /**
     * @param string $path
     * @param string $data
     * @return false|int
     */
    public function append(string $path, string $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }


    /**
     * @param string $path
     * @return false|string
     */
    public function shared(string $path)
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = fread($handle, $this->size($path) ?: 1);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * You can easily check mime type of an internet resource
     *
     * @param string $url
     * @return bool|string
     */
    public function getUrlMimeType(string $url): bool|string
    {
        $buffer = file_get_contents($url);
        return (new \finfo(FILEINFO_MIME_TYPE))->buffer($buffer);
    }

    /**
     * @param string $path
     * @param int|null $mode
     * @return bool|string
     */
    public function chmod(string $path, int|null $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }

        return substr(sprintf('%o', fileperms($path)), -4);
    }

    /**
     * Permissions as octal value
     *
     * @param string $file
     * @return int|bool
     */
    public function filePerm(string $file): int|bool
    {
        return substr(sprintf('%o', fileperms($file)), -4);
    }

    /**
     * @return array|false|mixed
     */
    public function getDisks()
    {
        if(PHP_OS_FAMILY === 'Windows') {
            $command = shell_exec('fsutil fsinfo drives');

            $disks = str_word_count($command,1);

            if($disks[0] !== 'Drives') {
                return false;
            }

            unset($disks[0]);

            foreach ($disks as $key => $value) {
                $disks[$key] = $value . ':\\';
            }

            return $disks;
        }

        $unixCmd = shell_exec('mount');

        $cmdExp = explode(' ',$unixCmd);

        $unixDisk = [];

        foreach ($cmdExp as $value) {
            if(substr($value,'/dev/')) {
                $unixDisk[] = $value;
            }
        }

        return $unixDisk;
    }

    /**
     * @param $byte
     * @return string
     */
    public function convertBytes($byte)
    {
        $types = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];

        $exp = $types ? floor(log($byte) / log(1024)) : 0;

        return sprintf('%.2f '. $types[$exp],($byte / (1024 ** floor($exp))));

    }

    /**
     * @param string $direction
     * @param bool $total
     * @return string
     */
    public function freeDiskSpace(string $direction, bool $total = false)
    {
        if($total) {
            return $this->convertBytes(disk_total_space($direction));
        }

        return $this->convertBytes(disk_free_space($direction));
    }

}