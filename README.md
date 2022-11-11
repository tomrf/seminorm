# seminorm - pdo sql query builder and executor

[![PHP Version Require](http://poser.pugx.org/tomrf/seminorm/require/php?style=flat-square)](https://packagist.org/packages/tomrf/seminorm) [![Latest Stable Version](http://poser.pugx.org/tomrf/seminorm/v?style=flat-square)](https://packagist.org/packages/tomrf/seminorm) [![License](http://poser.pugx.org/tomrf/seminorm/license?style=flat-square)](https://packagist.org/packages/tomrf/seminorm)

PHP SQL query builder and executor, using PDO.

📔 [Go to documentation](#documentation)

## Installation
Installation via composer:

```bash
composer require tomrf/seminorm
```

## Usage
```php
$db = new \Tomrf\Seminorm\Seminorm(
   new PdoConnection(
       PdoConnection::dsn(
           'mysql',
           'my_database',
           'localhost',
       ),
       'username',
       'password',
       []           // array of PDO options, ATTR_PERSISTENT, ATTR_ERRMODE etc..
   ),
   new Factory(QueryBuilder::class),
   new Factory(PdoQueryExecutor::class),
   null,            // optional row class, defaults to array
   null,            // optional data value class, defaults to string
);

$rows = $db->execute(
    'SELECT * FROM `my_table` WHERE id = :id',
    [ 'id' => 1 ]
)->findMany();

$rowId = $db->execute(
    $db->query()->insertInto(
       'my_table', 
       [ 'name' => 'My Name', 'email' => 'mail@example.com' ]
    )
)->getLastInsertId();

...
```

## Testing
```bash
composer test
```

## License
This project is released under the MIT License (MIT).
See [LICENSE](LICENSE) for more information.

## Documentation
 - [Tomrf\Seminorm\Seminorm](#-tomrfseminormseminormclass)
   - [__construct](#__construct)
   - [getConnection](#getconnection)
   - [query](#query)
   - [execute](#execute)
   - [setLogger](#setlogger)
 - [Tomrf\Seminorm\Data\ImmutableArrayObject](#-tomrfseminormdataimmutablearrayobjectclass)
   - [__get](#__get)
   - [__isset](#__isset)
   - [offsetSet](#offsetset)
   - [offsetUnset](#offsetunset)
   - [offsetGet](#offsetget)
   - [offsetExists](#offsetexists)
   - [__construct](#__construct)
   - [append](#append)
   - [getArrayCopy](#getarraycopy)
   - [count](#count)
   - [getFlags](#getflags)
   - [setFlags](#setflags)
   - [asort](#asort)
   - [ksort](#ksort)
   - [uasort](#uasort)
   - [uksort](#uksort)
   - [natsort](#natsort)
   - [natcasesort](#natcasesort)
   - [unserialize](#unserialize)
   - [serialize](#serialize)
   - [__serialize](#__serialize)
   - [__unserialize](#__unserialize)
   - [getIterator](#getiterator)
   - [exchangeArray](#exchangearray)
   - [setIteratorClass](#setiteratorclass)
   - [getIteratorClass](#getiteratorclass)
   - [__debugInfo](#__debuginfo)
 - [Tomrf\Seminorm\Data\Row](#-tomrfseminormdatarowclass)
   - [toArray](#toarray)
   - [toJson](#tojson)
   - [__get](#__get)
   - [__isset](#__isset)
   - [offsetSet](#offsetset)
   - [offsetUnset](#offsetunset)
   - [offsetGet](#offsetget)
   - [offsetExists](#offsetexists)
   - [__construct](#__construct)
   - [append](#append)
   - [getArrayCopy](#getarraycopy)
   - [count](#count)
   - [getFlags](#getflags)
   - [setFlags](#setflags)
   - [asort](#asort)
   - [ksort](#ksort)
   - [uasort](#uasort)
   - [uksort](#uksort)
   - [natsort](#natsort)
   - [natcasesort](#natcasesort)
   - [unserialize](#unserialize)
   - [serialize](#serialize)
   - [__serialize](#__serialize)
   - [__unserialize](#__unserialize)
   - [getIterator](#getiterator)
   - [exchangeArray](#exchangearray)
   - [setIteratorClass](#setiteratorclass)
   - [getIteratorClass](#getiteratorclass)
   - [__debugInfo](#__debuginfo)
 - [Tomrf\Seminorm\Data\Value](#-tomrfseminormdatavalueclass)
   - [__construct](#__construct)
   - [__toString](#__tostring)
   - [asString](#asstring)
   - [asInt](#asint)
   - [asFloat](#asfloat)
   - [asBool](#asbool)
   - [isNumeric](#isnumeric)
   - [isInt](#isint)
   - [isString](#isstring)
   - [isBool](#isbool)
   - [isNull](#isnull)
   - [getType](#gettype)
 - [Tomrf\Seminorm\Factory\Factory](#-tomrfseminormfactoryfactoryclass)
   - [__construct](#__construct)
   - [make](#make)
 - [Tomrf\Seminorm\Pdo\PdoConnection](#-tomrfseminormpdopdoconnectionclass)
   - [__construct](#__construct)
   - [getPdo](#getpdo)
   - [getOptions](#getoptions)
   - [isConnected](#isconnected)
   - [getDsn](#getdsn)
   - [getUsername](#getusername)
   - [dsn](#dsn)
   - [connect](#connect)
   - [disconnect](#disconnect)
   - [__debugInfo](#__debuginfo)
 - [Tomrf\Seminorm\Pdo\PdoQueryExecutor](#-tomrfseminormpdopdoqueryexecutorclass)
   - [__construct](#__construct)
   - [getRowCount](#getrowcount)
   - [getLastInsertId](#getlastinsertid)
   - [execute](#execute)
   - [findOne](#findone)
   - [findMany](#findmany)
 - [Tomrf\Seminorm\QueryBuilder\QueryBuilder](#-tomrfseminormquerybuilderquerybuilderclass)
   - [__toString](#__tostring)
   - [selectFrom](#selectfrom)
   - [insertInto](#insertinto)
   - [update](#update)
   - [deleteFrom](#deletefrom)
   - [set](#set)
   - [setRaw](#setraw)
   - [setFromArray](#setfromarray)
   - [alias](#alias)
   - [join](#join)
   - [limit](#limit)
   - [offset](#offset)
   - [onDuplicateKey](#onduplicatekey)
   - [getQuery](#getquery)
   - [getQueryParameters](#getqueryparameters)
   - [orderByAsc](#orderbyasc)
   - [orderByDesc](#orderbydesc)
   - [select](#select)
   - [selectAs](#selectas)
   - [selectRaw](#selectraw)
   - [selectRawAs](#selectrawas)
   - [where](#where)
   - [whereRaw](#whereraw)
   - [whereColumnRaw](#wherecolumnraw)
   - [whereEqual](#whereequal)
   - [whereNotEqual](#wherenotequal)
   - [whereNull](#wherenull)
   - [whereNotNull](#wherenotnull)
 - [Tomrf\Seminorm\Sql\SqlCompiler](#-tomrfseminormsqlsqlcompilerclass)
   - [getQuery](#getquery)
   - [getQueryParameters](#getqueryparameters)

### 📂 Tomrf\Seminorm\Seminorm::class

#### __construct()

```php
public function __construct(
    Tomrf\Seminorm\Pdo\PdoConnection $connection,
    Tomrf\Seminorm\Factory\Factory $queryBuilderFactory,
    Tomrf\Seminorm\Factory\Factory $queryExecutorFactory,
    ?string $rowClass = '',
    ?string $valueClass = ''
): void
```

#### getConnection()

Return the active connection.

```php
public function getConnection(): Tomrf\Seminorm\Pdo\PdoConnection

```

#### query()

```php
public function query(): Tomrf\Seminorm\QueryBuilder\QueryBuilder
```

#### execute()

```php
public function execute(
    Tomrf\Seminorm\Interface\QueryBuilderInterface|string $query,
    array $parameters = []
): Tomrf\Seminorm\Pdo\PdoQueryExecutor

@param    array<int|string,mixed> $parameters
```

#### setLogger()

Sets a logger.

```php
public function setLogger(
    Psr\Log\LoggerInterface $logger
): void

@param    \Tomrf\Seminorm\LoggerInterface $logger
```


***

### 📂 Tomrf\Seminorm\Data\ImmutableArrayObject::class

#### __get()

```php
public function __get(
    string $name
): mixed
```

#### __isset()

```php
public function __isset(
    mixed $name
): void
```

#### offsetSet()

```php
public function offsetSet(
    mixed $key,
    mixed $value
): void

@SuppressWarnings (PHPMD.UnusedFormalParameter)
```

#### offsetUnset()

```php
public function offsetUnset(
    mixed $key
): void

@SuppressWarnings (PHPMD.UnusedFormalParameter)
```

#### offsetGet()

```php
public function offsetGet(
    mixed $key
): mixed
```

#### offsetExists()

```php
public function offsetExists(
    mixed $key
): bool
```

#### __construct()

```php
public function __construct(
    object|array $array = [],
    int $flags = 0,
    string $iteratorClass = 'ArrayIterator'
): void
```

#### append()

```php
public function append(
    mixed $value
): void
```

#### getArrayCopy()

```php
public function getArrayCopy(): void
```

#### count()

```php
public function count(): void
```

#### getFlags()

```php
public function getFlags(): void
```

#### setFlags()

```php
public function setFlags(
    int $flags
): void
```

#### asort()

```php
public function asort(
    int $flags = 0
): void
```

#### ksort()

```php
public function ksort(
    int $flags = 0
): void
```

#### uasort()

```php
public function uasort(
    callable $callback
): void
```

#### uksort()

```php
public function uksort(
    callable $callback
): void
```

#### natsort()

```php
public function natsort(): void
```

#### natcasesort()

```php
public function natcasesort(): void
```

#### unserialize()

```php
public function unserialize(
    string $data
): void
```

#### serialize()

```php
public function serialize(): void
```

#### __serialize()

```php
public function __serialize(): void
```

#### __unserialize()

```php
public function __unserialize(
    array $data
): void
```

#### getIterator()

```php
public function getIterator(): void
```

#### exchangeArray()

```php
public function exchangeArray(
    object|array $array
): void
```

#### setIteratorClass()

```php
public function setIteratorClass(
    string $iteratorClass
): void
```

#### getIteratorClass()

```php
public function getIteratorClass(): void
```

#### __debugInfo()

```php
public function __debugInfo(): void
```

### 📂 Tomrf\Seminorm\Data\Row::class

#### toArray()

```php
public function toArray(): array

@return   array <string,mixed>
```

#### toJson()

```php
public function toJson(): string
```

#### __get()

```php
public function __get(
    string $name
): mixed
```

#### __isset()

```php
public function __isset(
    mixed $name
): void
```

#### offsetSet()

```php
public function offsetSet(
    mixed $key,
    mixed $value
): void

@SuppressWarnings (PHPMD.UnusedFormalParameter)
```

#### offsetUnset()

```php
public function offsetUnset(
    mixed $key
): void

@SuppressWarnings (PHPMD.UnusedFormalParameter)
```

#### offsetGet()

```php
public function offsetGet(
    mixed $key
): mixed
```

#### offsetExists()

```php
public function offsetExists(
    mixed $key
): bool
```

#### __construct()

```php
public function __construct(
    object|array $array = [],
    int $flags = 0,
    string $iteratorClass = 'ArrayIterator'
): void
```

#### append()

```php
public function append(
    mixed $value
): void
```

#### getArrayCopy()

```php
public function getArrayCopy(): void
```

#### count()

```php
public function count(): void
```

#### getFlags()

```php
public function getFlags(): void
```

#### setFlags()

```php
public function setFlags(
    int $flags
): void
```

#### asort()

```php
public function asort(
    int $flags = 0
): void
```

#### ksort()

```php
public function ksort(
    int $flags = 0
): void
```

#### uasort()

```php
public function uasort(
    callable $callback
): void
```

#### uksort()

```php
public function uksort(
    callable $callback
): void
```

#### natsort()

```php
public function natsort(): void
```

#### natcasesort()

```php
public function natcasesort(): void
```

#### unserialize()

```php
public function unserialize(
    string $data
): void
```

#### serialize()

```php
public function serialize(): void
```

#### __serialize()

```php
public function __serialize(): void
```

#### __unserialize()

```php
public function __unserialize(
    array $data
): void
```

#### getIterator()

```php
public function getIterator(): void
```

#### exchangeArray()

```php
public function exchangeArray(
    object|array $array
): void
```

#### setIteratorClass()

```php
public function setIteratorClass(
    string $iteratorClass
): void
```

#### getIteratorClass()

```php
public function getIteratorClass(): void
```

#### __debugInfo()

```php
public function __debugInfo(): void
```

### 📂 Tomrf\Seminorm\Data\Value::class

#### __construct()

```php
public function __construct(
    string|int|float|bool|null $data
): void
```

#### __toString()

```php
public function __toString(): string
```

#### asString()

```php
public function asString(): string
```

#### asInt()

```php
public function asInt(): int
```

#### asFloat()

```php
public function asFloat(): float
```

#### asBool()

```php
public function asBool(): bool
```

#### isNumeric()

```php
public function isNumeric(): bool
```

#### isInt()

```php
public function isInt(): bool
```

#### isString()

```php
public function isString(): bool
```

#### isBool()

```php
public function isBool(): bool
```

#### isNull()

```php
public function isNull(): bool
```

#### getType()

```php
public function getType(): string
```

### 📂 Tomrf\Seminorm\Factory\Factory::class

#### __construct()

```php
public function __construct(
    string $class
): void

@param    class-string $class
```

#### make()

```php
public function make(
    mixed $params
): mixed
```

### 📂 Tomrf\Seminorm\Pdo\PdoConnection::class

#### __construct()

```php
public function __construct(
    PDO|string $dsnOrPdo,
    ?string $username = '',
    ?string $password = '',
    ?array $options = []
): void

@param    \PDO|string $dsnOrPdo DSN string or an existing PDO object
@param    null|array<int,int> $options PDO options array
```

#### getPdo()

Get the PDO resource object for this connection.

```php
public function getPdo(): ?PDO

```

#### getOptions()

Get PDO options array for this connection.

```php
public function getOptions(): ?array

@return   null|array<int,int>
```

#### isConnected()

Returns true if database connection has been established.

```php
public function isConnected(): bool

```

#### getDsn()

Get the value of DSN.

```php
public function getDsn(): ?string

```

#### getUsername()

Get the value of username.

```php
public function getUsername(): ?string

```

#### dsn()

Static helper function to build DSN string for PDO.

```php
public static function dsn(
    string $driver,
    string $dbname,
    ?string $host = '',
    int $port = 3306,
    string $charset = 'utf8mb4'
): string

```

#### connect()

Connect to the database if not already connected.

```php
public function connect(): void

@throws   \RuntimeException
```

#### disconnect()

Disconnect from the database.

```php
public function disconnect(): void

```

#### __debugInfo()

Mask password when dumping.

```php
public function __debugInfo(): array

@return   array<string,string|null|array<int,int>>
```

### 📂 Tomrf\Seminorm\Pdo\PdoQueryExecutor::class

#### __construct()

```php
public function __construct(
    Tomrf\Seminorm\Pdo\PdoConnection $connection,
    ?string $rowClass = '',
    ?string $valueClass = ''
): void
```

#### getRowCount()

Returns the number of rows affected by the last SQL statement.

```php
public function getRowCount(): int

```

#### getLastInsertId()

Returns the last inserted row ID as string.

```php
public function getLastInsertId(): string|false

```

#### execute()

Prepare and execute PDOStatement from an instance of
QueryBuilderInterface.

```php
public function execute(
    Tomrf\Seminorm\Interface\QueryBuilderInterface|string $query,
    array $parameters = []
): static

@throws   \PDOException
```

#### findOne()

Fetch next row from the result set as Row.

```php
public function findOne(): object|array|null

@return   null|(null|object|string)[]|object
```

#### findMany()

Fetch all rows from query result set.

```php
public function findMany(): object|array

@return   array<int,(null|object|string)[]|object>
```

### 📂 Tomrf\Seminorm\QueryBuilder\QueryBuilder::class

#### __toString()

```php
public function __toString(): string
```

#### selectFrom()

```php
public function selectFrom(
    string $table,
    string $columns
): static
```

#### insertInto()

Insert a row into a table

```php
public function insertInto(
    string $table,
    array $values = []
): static

@param    string $table
@param    array<string,int|string|float|null> $values
@throws   \InvalidArgumentException
```

#### update()

```php
public function update(
    string $table,
    array $values = []
): static

@param    array<string,int|string|float|null> $values
@throws   \InvalidArgumentException
```

#### deleteFrom()

```php
public function deleteFrom(
    string $table
): static
```

#### set()

```php
public function set(
    string $column,
    string|int|float $value
): static
```

#### setRaw()

```php
public function setRaw(
    string $column,
    string $expression
): static
```

#### setFromArray()

```php
public function setFromArray(
    array $values
): static

@param    array<string,null|float|int|string> $values
@throws   \InvalidArgumentException
```

#### alias()

```php
public function alias(
    string $expression,
    string $alias
): static
```

#### join()

```php
public function join(
    string $table,
    string $joinCondition,
    ?string $joinType = ''
): static
```

#### limit()

```php
public function limit(
    int $limit,
    ?int $offset = null
): static
```

#### offset()

```php
public function offset(
    int $offset
): static
```

#### onDuplicateKey()

```php
public function onDuplicateKey(
    string $expression
): static
```

#### getQuery()

```php
public function getQuery(): string
```

#### getQueryParameters()

```php
public function getQueryParameters(): array

@return   (null|bool|float|int|string)[]
```

#### orderByAsc()

```php
public function orderByAsc(
    string $column
): static
```

#### orderByDesc()

```php
public function orderByDesc(
    string $column
): static
```

#### select()

```php
public function select(
    string $columns
): static
```

#### selectAs()

```php
public function selectAs(
    string $expression,
    string $alias
): static
```

#### selectRaw()

```php
public function selectRaw(
    string $expressions
): static
```

#### selectRawAs()

```php
public function selectRawAs(
    string $expression,
    string $alias
): static
```

#### where()

```php
public function where(
    string $column,
    string $operator,
    string|int|float $value
): static
```

#### whereRaw()

```php
public function whereRaw(
    string $expression
): static
```

#### whereColumnRaw()

```php
public function whereColumnRaw(
    string $column,
    string $expression
): static
```

#### whereEqual()

```php
public function whereEqual(
    string $column,
    string|int|float $value
): static
```

#### whereNotEqual()

```php
public function whereNotEqual(
    string $column,
    string|int|float $value
): static
```

#### whereNull()

```php
public function whereNull(
    string $column
): static
```

#### whereNotNull()

```php
public function whereNotNull(
    string $column
): static
```

### 📂 Tomrf\Seminorm\Sql\SqlCompiler::class

#### getQuery()

```php
public function getQuery(): string
```

#### getQueryParameters()

```php
public function getQueryParameters(): array

@return   (null|bool|float|int|string)[]
```



***

_Generated 2022-11-11T01:32:36+01:00 using 📚[tomrf/readme-gen](https://packagist.org/packages/tomrf/readme-gen)_
