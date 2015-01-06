# WP Autoloader

[WordPress Autoloader](https://bitbucket.org/holyfield/wp-autoloader) makes WordPress developers life really easy. This plugin loads automatically PHP classes. Many developers writing object-oriented applications create one PHP source file per class definition. One of the biggest annoyances is having to write a long list of needed includes at the beginning of each script (one for each class). With WordPress Autoloader, this is no longer necessary.

If you put all your classes into **/lib** folder on your theme or plugin root directory, then all these classes are loaded automatically. Additionally you can define class search path or your own method to automatically load your classes in case you are trying to use a class/interface which hasn't been defined yet. By using WordPress Autoloader the scripting engine is given a last chance to load the class before PHP fails with an error.

The great advantage of automatically loaded classes is also perfomance increase, wich means faster loading. Loading unneeded files wastes server resources and increases webpage load time.

Please read [documentation](https://bitbucket.org/holyfield/wp-autoloader/wiki) for detailed instructions.