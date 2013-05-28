Hummingbird
===========
Lightweight, extensible web framework.

**Current version:** 2.0

### Requirements ###

 - PHP 5.x
 - Apache 2.x with mod_rewrite enabled

*Note: Some plugins may have additional requirements*

### Installation ###

Just unzip the distribution package and you're done.

### Configuration ###

To configure your hummingbird instance just edit the `config.inc.php` file inside the `/include` folder by
setting the appropiate parameters on the `$settings` array. There are three major sections by default:

 - *development* - Set development-specific options here (most likely local host/db).
 - *production* - Set production-specific options here, e.g. the real thing.
 - *shared* - Set global options here.

### Credits ###

**Lead coder:** biohzrdmx [&lt;github.com/biohzrdmx&gt;](http://github.com/biohzrdmx)

## License ##
Copyright &copy; 2013 biohzrdmx

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.