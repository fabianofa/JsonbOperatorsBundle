JsonbBundle
----

PostgreSQL 9.4 introduced the new data type jsonb. With it some documented operators such as ->, ->> and #>>{} are available to access or retrieve values within a jsonb column data. Since Symfony uses Doctrine as default and Doctrine uses its own query language DQL, the operators aren't available natively. You can also [check]https://www.postgresql.org/docs/current/static/functions-json.html

PostgreSQL documentation about json functions and operators: https://www.postgresql.org/docs/current/static/functions-json.html

This bundle adds some custom functions to allow developers to use such operators.

1. Installation
2. Adding custom functions to config.yml
3. Documentation
4. Planned improvements

============

# 1. Installation
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require fabianofa/jsonboperators-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

# 2. Adding custom functions to config.yml(php/xml)
-------------------------
You must add the new functions to be used with DQL into your project's config.yml file, or equivalent if you're using XML, annotations or PHP for config files. If any of the structure is already declared, just append the missing settings.

```
doctrine:
   orm:
        dql:
            string_functions:
                jsonbbykey: fabianofa\JsonbOperatorsBundle\Doctrine\Query\JsonbByKey
                jsonbbykeytext: fabianofa\JsonbOperatorsBundle\Doctrine\Query\JsonbByKeyText
                jsonbbypath: fabianofa\JsonbOperatorsBundle\Doctrine\Query\JsonbByPath
```

# 3. Documentation
-------------------------

From now on you can call one of the functions in your query builder definition in methods as `select` or `andWhere`/`where`, such as:

```
$query = $entityManager->createQueryBuilder("f")
                        ->from("AppBundle:Entity", "e")
                        ->select("jsonbbykeytext(jsoncolumn, 'jsonpropertykey')")
                        ->where("e.id = :id")
                        ->setParameter(":id", 5);
```

## Functions and operators:

* All functions take two parameters.
* The first parameter is the column name typed as jsonb
* The second parameter can be:
  * a single parameter value. Eg: `jsonbbykeytext(jsonbcolumn, 'key')`
  * a string containing a path of json keys separated by comma (,): Eg: `jsonbbypath(jsonbcolumn, 'key1,key2')`


### jsonbykey()

  ##### Example 1 (single key):

  DQL: `->select("jsonbykey(jsonbcolumn, 'key')`

  SQL Equivalent: `SELECT jsonbcolumn->'key' FROM ...`

  ##### Example 2 (multiple keys):

  DQL: `->select("jsonbykey(jsonbcolumn, 'key,key2')`

  SQL Equivalent: `SELECT jsonbcolumn->'key'->'key2' FROM ...`

---

### jsonbykeytext()

  ##### Example 1 (single key):

  DQL: `->select("jsonbykeytext(jsonbcolumn, 'key')`

  SQL Equivalent: `SELECT jsonbcolumn->>'key' FROM ...`

  ##### Example 2 (multiple keys):

  DQL: `->select("jsonbykeytext(jsonbcolumn, 'key,key2')`

  SQL Equivalent: `SELECT jsonbcolumn->>'key'->>'key2' FROM ...`

---

### jsonbbypath()

  ##### Example 1 (single key):

  DQL: `->select("jsonbbypath(jsonbcolumn, 'key')`

  SQL Equivalent: `SELECT jsonbcolumn#>>'{key}' FROM ...`

  ##### Example 2 (multiple keys):

  DQL: `->select("jsonbykeytext(jsonbcolumn, 'key,key2')`

  SQL Equivalent: `SELECT jsonbcolumn#>>'{key, key2}' FROM ...`

---

# Planned improvements
There are more operators listed in the official PostgreSQL documentation. The goal is to offer all the operators available. Some native functions may be already be implemented in Doctrine therefore you're encourage to use the native solution instead this package.