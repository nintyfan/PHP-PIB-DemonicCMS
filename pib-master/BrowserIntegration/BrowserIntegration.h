/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2018 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Sterling Hughes <sterling@php.net>                           |
  +----------------------------------------------------------------------+
*/

#ifndef PHP_BrowserIntegration_H
#define PHP_BrowserIntegration_H

#if HAVE_BrowserIntegration

extern zend_module_entry BrowserIntegration_module_entry;
#define phpext_BrowserIntegration_ptr &BrowserIntegration_module_entry

/* Bzip2 includes */
#include <BrowserIntegration.h>

#else
#define phpext_BrowserIntegration_ptr NULL
#endif

#include "php_version.h"
#define PHP_BrowserIntegration_VERSION PHP_VERSION

PHP_BrowserIntegration_API void _php_browser_integration_execute_js(const char *js_string, php_stream_context *context STREAMS_DC);

#define php_browser_integration_execute_js(string)	_php_browser_integration_execute_js((string), NULL STREAMS_CC)

#endif


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
