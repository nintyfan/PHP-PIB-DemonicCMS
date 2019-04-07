dnl config.m4 for extension BrowserIntegration

PHP_ARG_WITH(BrowserIntegration, for BrowserIntegration support,
[  --with-BrowserIntegration[=DIR]          Include BrowserIntegration support])

if test "$PHP_BrowserIntegration" != "no"; then
  if test -r $PHP_BrowserIntegration/include/BrowserIntegration.h; then
    BZIP_DIR=$PHP_BrowserIntegration
  else
    AC_MSG_CHECKING(for BrowserIntegration in default path)
    for i in /usr/local /usr; do
      if test -r $i/include/bzlib.h; then
        BZIP_DIR=$i
        AC_MSG_RESULT(found in $i)
        break
      fi
    done
  fi

  if test -z "$BZIP_DIR"; then
    AC_MSG_RESULT(not found)
    AC_MSG_ERROR(Please reinstall the BZip2 distribution)
  fi


  PHP_NEW_EXTENSION(BrowserIntegration, BrowserIntegration.c, $ext_shared)
  PHP_SUBST(BrowserIntegration_SHARED_LIBADD)
fi
