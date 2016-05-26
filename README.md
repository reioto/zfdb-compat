# zfdb-compat

This Library has included the functions that  had been used often in ZF1.

Example: Adapter
- fetchAll
- fetchRow
- fetchOne
- fetchPairs
- getConnection
- beginTransaction
- commit
- rollback
- lastInsertId

## Basic Usage
- Execute the SQL string : Adapter
- Assemble the SQL and execute : Sql
- ORM : AbstractTable

## Recommend
- Execute Select strings : Adapter
- Execute Update, Insert : Table or Subclass of AbstractTable
- Assemble the SQL and execute : Sql
