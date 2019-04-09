<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// The environment variables set here are used by PHPUnit tests.
// To set the the acceptance tests env vars, use the .env file.

putenv(
    'OAUTH2_ENCRYPTION_KEY=def00000efe807ae7778341feb750c67b8c70fc7b71741bc469fc6362e37bd846f5c97b8f5d380ed6481870dbbe612fd7807c70179963154180cdb0aca892a757bccd1f3'
);

// The "OAUTH2_PRIVATE_KEY" and "OAUTH2_PUBLIC_KEY" are set here because the \n in the phpunit.xml.dist
// is converted to a space and breaks the key.

// Generated using: `openssl genrsa -out private.no-vcs.key 2048`
putenv(
    'OAUTH2_PRIVATE_KEY=-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEA3h25YzyGrtwXvHKD6bVbhlp2Owct/GPsi0tKBjcO9+bBgNCr
S2dVhNmrHL06limLywan7LOoGp3oWua1O0PEDWWRHnuHxg6w/29rNPM0tEsNhkOR
wirU/U+wT/GR7Xv9lOt6ho52NpbDmLfR1Ztu+dCPdipd2lVc1qvgWGTEebfDDSc8
bMeoy7kfWLmFyINcmnhrf0vE6i6OGR5tymsVQSYDX7d6lW7gGyITBSBe+AE2Oxzp
lZcKFDi5rn30by4kUzC1DFn0kLz0Uza/w0AHq7DokR8LT9O49iipG9W+Irmb5fmP
GlkJjezXE6YBoQotQij1/2dfhsd3ePFakgiFbwIDAQABAoIBAQCrqVlP4sVMevk1
QWPFyc4UhfF+KUxXFXsJJITvTLoayZKfVaYtPo2PgRKHwY934mAR5vD9NNQkzgl3
x3oldXgdynNflaUXYt9Uau32HEiNVrv7GlmaMLQvmdjv0Akx+3O+Fke5mnyuL9K3
Qsm/RsN7+r4FWzTxuqtnlaprZmWakwRiBtBCbUxPZiitgDK4gwClLJhHwU3t57+7
R0Yca6Bgvyqrr5vo5Y1woDm0vnqhD3jz+sG/YeNAiNR8n+ihvhh3bxnUPJPFiPkZ
ymjjE63A42YS8nl/RF0WxNJARWlp0h16pCm5nQQh/KW3dy5OIaG+9NQOCrLzz6/A
piSc7esBAoGBAPH0rpnI5Iab1a3ZZ+4C+qnZ0NM0nE857MUwnDHtPqskkc4hzfNp
eHE7JpwnMjwCs3KI+mtmm8yIyMUN6W2U6Wp+syE+zZmXR6Mcj0aJCYmANPLGXiZD
iYP+pgK9RHn7vGxTwIvTSKpwgKVheCaSpcOcFaExU6zDiel49cnxFwGvAoGBAOsC
PIShsidZWfyDKmktc6BIhJdzcgBH/6VpayDyQubuO72VCWcGG9K02cK3hEMqoEVK
bA3yeSZX5KoHVPzpnqadsa5FQyDzWVLGxloTSCX6y6GTWwxm7JbnOdKMa/T+7lmW
uQvcZPYkbId6F5Zut6CQ0wkY7V8huOG1G+AwHmhBAoGBANlR3EFkn14IOjScQS0W
n+5PJbGOX5cJgBDdSG3Pmao+fneXBVTMNtH9PwCidAxSoLsPLV2qG+XqNepIRJGY
Xs64XkxODH0X09A9prLEAzztWqF0arwTjUBxSMrNFOQ7p1HlJ9xvOaVcxy/EDBcZ
QKohV4wgsfdK6mQ5sQpg8TkhAoGAY5+RFBCPu0qPcRIReoDAEWRsgN95plDfOLnV
piPM5KR9QsLZN4lJZiswXPD66pY5VuZTpB3z6aM81i9Bge1vSeZzmhLWgDNo3ERT
dX/TB/anOBmFcV54UQg4ZN8OLM+dLvFMBJErY4TRVSUWtri8sx5xt9uPVNKw0025
QXJkkYECgYBIA8jVuFHx/r7TxhihVjfK6uQUWLNGJXEPgbcDhg5UfFX6KfGCLCjW
M85psxVT4rpPYVlsZT16AkahL76nggNtzbzP+6iliBita5AdQ3x9HXjhKaMnm+Ut
4v8ymX7uyJZ4F2VZZfUYSwEsrQgJn2MKEB5dWv+UNK0VaITgLNZ/XA==
-----END RSA PRIVATE KEY-----'
);

// Generated using: `openssl rsa -in private.no-vcs.key -pubout -out public.no-vcs.key`
putenv(
    'OAUTH2_PUBLIC_KEY=-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3h25YzyGrtwXvHKD6bVb
hlp2Owct/GPsi0tKBjcO9+bBgNCrS2dVhNmrHL06limLywan7LOoGp3oWua1O0PE
DWWRHnuHxg6w/29rNPM0tEsNhkORwirU/U+wT/GR7Xv9lOt6ho52NpbDmLfR1Ztu
+dCPdipd2lVc1qvgWGTEebfDDSc8bMeoy7kfWLmFyINcmnhrf0vE6i6OGR5tymsV
QSYDX7d6lW7gGyITBSBe+AE2OxzplZcKFDi5rn30by4kUzC1DFn0kLz0Uza/w0AH
q7DokR8LT9O49iipG9W+Irmb5fmPGlkJjezXE6YBoQotQij1/2dfhsd3ePFakgiF
bwIDAQAB
-----END PUBLIC KEY-----'
);
