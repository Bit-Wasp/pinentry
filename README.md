pinentry
=========

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Bit-Wasp/pinentry/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Bit-Wasp/pinentry/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Bit-Wasp/pinentry/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Bit-Wasp/pinentry/?branch=master)
[![Build Status](https://travis-ci.org/Bit-Wasp/pinentry.svg?branch=master)](https://travis-ci.org/Bit-Wasp/pinentry)

This package allows PHP command line applications to prompt
users for a passphrase / pin using the gpgtools `pinentry` program.

https://www.gnupg.org/related_software/pinentry/index.html

The PinRequest class allows passphrase requests to be parameterized 
(messages, titles, button text), or to request a confirmation password
also.
