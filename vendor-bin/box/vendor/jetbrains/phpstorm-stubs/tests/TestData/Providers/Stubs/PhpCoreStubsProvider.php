<?php
declare(strict_types=1);

namespace StubTests\TestData\Providers\Stubs;

use StubTests\Model\CommonUtils;

class PhpCoreStubsProvider
{
    public static array $StubDirectoryMap = [
        'CORE' => [
            'Core',
            'date',
            'filter',
            'fpm',
            'hash',
            'meta',
            'pcre',
            'random',
            'Phar',
            'Reflection',
            'regex',
            'session',
            'SPL',
            'standard',
            'superglobals',
            'tokenizer'
        ],
        'BUNDLED' => [
            'apache',
            'bcmath',
            'calendar',
            'ctype',
            'dba',
            'exif',
            'fileinfo',
            'ftp',
            'gd',
            'iconv',
            'intl',
            'json',
            'mbstring',
            'pcntl',
            'PDO',
            'posix',
            'shmop',
            'sockets',
            'sqlite3',
            'sysvmsg',
            'sysvsem',
            'sysvshm',
            'xmlrpc',
            'zlib'
        ],
        'EXTERNAL' => [
            'aerospike',
            'bz2',
            'curl',
            'dom',
            'enchant',
            'gettext',
            'gmp',
            'imap',
            'ldap',
            'libxml',
            'mcrypt',
            'mssql',
            'mysql',
            'mysqli',
            'oci8',
            'odbc',
            'openssl',
            'pdo_ibm',
            'pdo_mysql',
            'pdo_pgsql',
            'pdo_sqlite',
            'pgsql',
            'pspell',
            'readline',
            'recode',
            'SimpleXML',
            'snmp',
            'soap',
            'sodium',
            'sybase',
            'tidy',
            'wddx',
            'xml',
            'xmlreader',
            'xmlwriter',
            'xsl',
            'Zend OPcache',
            'zip'
        ],
        'PECL' => [
            'apcu',
            'ast',
            'brotli',
            'crypto',
            'cubrid',
            'decimal',
            'ds',
            'eio',
            'event',
            'expect',
            'gearman',
            'geoip',
            'gmagick',
            'http',
            'ibm_db2',
            'imagick',
            'inotify',
            'interbase',
            'leveldb',
            'libevent',
            'LuaSandbox',
            'lzf',
            'mailparse',
            'memcache',
            'memcached',
            'ming',
            'mongo',
            'mongodb',
            'msgpack',
            'mysql_xdevapi',
            'ncurses',
            'oauth',
            'opentelemetry',
            'pam',
            'parallel',
            'Parle',
            'pcov',
            'pdflib',
            'pq',
            'pthreads',
            'radius',
            'rdkafka',
            'rpminfo',
            'simdjson',
            'simple_kafka_client',
            'solr',
            'SplType',
            'SQLite',
            'sqlsrv',
            'ssh2',
            'stats',
            'stomp',
            'svn',
            'swoole',
            'sync',
            'uopz',
            'uploadprogress',
            'uuid',
            'uv',
            'winbinder',
            'wincache',
            'xdiff',
            'xhprof',
            'xxtea',
            'yaf',
            'yaml',
            'yar',
            'zookeeper',
            'zstd'
        ],
        'OTHERS' => [
            'amqp',
            'blackfire',
            'cassandra',
            'com_dotnet',
            'couchbase',
            'couchbase_v2',
            'dio',
            'elastic_apm',
            'Ev',
            'fann',
            'FFI',
            'ffmpeg',
            'frankenphp',
            'geos',
            'gnupg',
            'grpc',
            'igbinary',
            'judy',
            'libsodium',
            'libvirt-php',
            'lua',
            'mapscript',
            'meminfo',
            'mosquitto-php',
            'mqseries',
            'newrelic',
            'phpdbg',
            'rar',
            'redis',
            'relay',
            'rrd',
            'SaxonC',
            'snappy',
            'suhosin',
            'svm',
            'v8js',
            'win32service',
            'xcache',
            'xdebug',
            'xlswriter',
            'zend',
            'ZendCache',
            'ZendDebugger',
            'ZendUtils',
            'zmq'
        ]
    ];

    /**
     * @return string[]
     */
    public static function getCoreStubsDirectories(): array
    {
        $coreStubs = [self::$StubDirectoryMap['CORE']];
        $coreStubs[] = self::$StubDirectoryMap['BUNDLED'];
        $coreStubs[] = self::$StubDirectoryMap['EXTERNAL'];
        return CommonUtils::flattenArray($coreStubs, false);
    }

    /**
     * @return string[]
     */
    public static function getNonCoreStubsDirectories(): array
    {
        $coreStubs = [self::$StubDirectoryMap['PECL']];
        $coreStubs[] = self::$StubDirectoryMap['OTHERS'];
        return CommonUtils::flattenArray($coreStubs, false);
    }
}
