<?php
namespace Lebran\Utils;

/**
 * Autoloader implement PSR - 4.
 *
 * @package    Utils
 * @version    2.0.0
 * @author     Roman Kritskiy <itoktor@gmail.com>
 * @license    GNU Licence
 * @copyright  2014 - 2015 Roman Kritskiy
 */
class Autoloader
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected $prefixes = [];

    /**
     * An associative array where the key is a absolute class name and the value is a path for class.
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return object Autoloader object.
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
        return $this;
    }

    /**
     * Un-register loader with SPL autoloader stack.
     *
     * @return object Autoloader object.
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'loadClass']);
        return $this;
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix  Namespace prefix.
     * @param string $dir     A base directory for class files in the namespace.
     * @param bool   $prepend If true, prepend the base directory to the stack instead of appending it.
     *
     * @return object Autoloader object.
     */
    public function addNamespace($prefix, $dir, $prepend = false)
    {
        $prefix = trim($prefix, '\\').'\\';
        $dir    = rtrim($dir, DIRECTORY_SEPARATOR).'/';

        if (!array_key_exists($prefix, $this->prefixes)) {
            $this->prefixes[$prefix] = [];
        }

        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $dir);
        } else {
            $this->prefixes[$prefix][] = $dir;
        }

        return $this;
    }

    /**
     * Adds a bases directory for a namespaces prefix.
     *
     * @param array $namespaces Array: 'prefix' => 'dir'.
     *
     * @return object Autoloader object.
     */
    public function addNamespaces(array $namespaces)
    {
        foreach ($namespaces as $prefix => $dir) {
            $this->addNamespace($prefix, $dir);
        }
        return $this;
    }

    /**
     * Get register prefixes.
     *
     * @return array 'prefix' => 'dir'.
     */
    public function getNamespaces()
    {
        return $this->prefixes;
    }

    /**
     * Adds a classpath. Each class will be added immediately loaded without search path.
     *
     * @param array $classes Array: absolute class name => path for file.
     *
     * @return object Autoloader object.
     */
    public function addClasses(array $classes)
    {
        $this->classes = array_merge($this->classes, $classes);
        return $this;
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     *
     * @return mixed The mapped file name on success, or boolean false on failure.
     */
    public function loadClass($class)
    {
        if (!empty($this->classes[$class]) && $this->requireFile($this->classes[$class])) {
            return true;
        }

        $prefix = $class;
        while (($pos = strrpos($prefix, '\\'))) {
            $prefix         = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);

            if ($this->loadMappedFile($prefix, $relative_class)) {
                return true;
            }

            $prefix = rtrim($prefix, '\\');
        }
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix         The namespace prefix.
     * @param string $relative_class The relative class name.
     *
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        if (!array_key_exists($prefix, $this->prefixes)) {
            return false;
        }

        foreach ($this->prefixes[$prefix] as $dir) {
            $file = $dir.str_replace('\\', '/', $relative_class).'.php';
            if ($this->requireFile($file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
     *
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }
}
