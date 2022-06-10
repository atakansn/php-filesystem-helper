# PHP File System Class

- ##### The most used Php file system functions in daily life have been turned into a class inspired by Laravel.


### All Functions

| Function name         | Description                                                                                            |
|-----------------------|--------------------------------------------------------------------------------------------------------|
| `symLink()`           | Create a symbolic link.                                                                                |
| `directoryExists()`   | Checks if a folder exists or creates a folder.                                                         |
| `prepend()`           | Appends data to the end of file content.                                                               |
| `getUrlMimeType()`    | Reads the extension of any url resource.                                                               |
| `filePerm()`          | Shows file permissions as octal values.                                                                |
| `deleteDirectories()` | Deletes multiple folders within the folder.                                                            |
| `basename()`          | Returns the specified filename.                                                                        |
| `dirname()`           | Returns the folder name of the file path.                                                              |
| `extension()`         | Gives the file extension.                                                                              |
| `exists()`            | Checks the existence of the file, true if exists, false otherwise.                                     |
| `move()`              | Moves the file to the folder with the new name.                                                        |
| `copy()`              | Copies the file to the folder with the new name.                                                       |
| `info()`              | Gives information about the file.                                                                      |
| `type()`              | Returns the file type.                                                                                 |
| `size()`              | Returns the size of the file .                                                                         |
| `lastModified()`      | Returns the last edited time of the file.                                                              |
| `isDirectory()`       | Tells if a file is a directory.                                                                        |
| `isReadable()`        | Tells if a file exists and is readable.                                                                |
| `isWritible()`        | Tells if a file is writable.                                                                           |
| `isFile()`            | Tells if a file is an ordinary file.                                                                   |
| `glob()`              | Finds file paths that match a pattern.                                                                 |
| `moveDirectory()`     | Moves the folder, checks for existence.                                                                |
| `deleteDirectory()`   | Deletes the folder, checks its existence, deletes it by doing detailed folder analysis.                |
| `directories()`       | Get all of the directories within a given directory.                                                   |
| `copyDirectory()`     | Copy a directory from one location to another.                                                         |
| `cleanDirectory()`    | Deletes all files in the folder..                                                                      |
| `delete()`            | Delete the file at a given path.                                                                       |
| `makeDirectory()`     | Creates folder with specified name, permission.                                                        |
| `filePut()`           | Returns file_put_contents().                                                                           |
| `replace()`           | Write the contents of a file, replacing it atomically if it already exists.                            |
| `replaceInFile()`     | Saves by replacing the data in an existing file..                                                      |
| `append()`            | If the filename file exists, the data is not overwritten, but appended to the end..                    |
| `chmod()`             | Sets the file privilege, if the $mod parameter is empty, it returns the current privilege of the file. |
| `getDisks()`          | Returns the disks of the machine on which PHP is installed.                                            |
| `freeDiskSpace()`     | Shows the free space on the disk. Returns the total disk space if the $total parameter is true.        |



