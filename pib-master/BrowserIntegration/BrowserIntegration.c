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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_bz2.h"

#if HAVE_BZ2

/* PHP Includes */
/* for fileno() */
#include <stdio.h>
#include <emscripten.h>

/* Internal error constants */
#define PHP_BZ_ERRNO   0
#define PHP_BZ_ERRSTR  1
#define PHP_BZ_ERRBOTH 2

static PHP_MINIT_FUNCTION(BrowserIntegration);
static PHP_MSHUTDOWN_FUNCTION(BrowserIntegration);
static PHP_MINFO_FUNCTION(BrowserIntegration);
static PHP_FUNCTION(execute_javascript);

/* {{{ arginfo */

ZEND_BEGIN_ARG_INFO(arginfo_execute_js, 0)
	ZEND_ARG_INFO(0, js_string)
ZEND_END_ARG_INFO()
/* }}} */

static const BrowserIntegration_function_entry bz2_functions[] = {
	PHP_FE(php_browser_integration_execute_js,       arginfo_bzopen)
	PHP_FE_END
};

zend_module_entry BrowserIntegration_module_entry = {
	STANDARD_MODULE_HEADER,
	"BrowserIntegration",
	BrowserIntegration_function,
	PHP_MINIT(BrowserIntegration),
	PHP_MSHUTDOWN(BrowserIntegration),
	NULL,
	NULL,
	PHP_MINFO(BrowserIntegration),
	PHP_BrowserIntegration_VERSION,
	STANDARD_MODULE_PROPERTIES
};


/* {{{ BZip2 stream implementation */
void php_browser_integration_execute_js(const char *js_string)
{
   ASM_JS()l
}
/* }}} */

#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: fdm=marker
 * vim: noet sw=4 ts=4
 */
