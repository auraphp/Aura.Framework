<?php
namespace Aura\Framework;

use org\bovigo\vfs\vfsStream as Vfs;

class VfsSystem
{
    public static function create($root)
    {
        Vfs::setup($root);
        $root = Vfs::url($root);
        mkdir("$root/config");
        mkdir("$root/include");
        mkdir("$root/package");
        mkdir("$root/vendor");
        mkdir("$root/web");
        mkdir("$root/tmp");
        return $root;
    }
}
