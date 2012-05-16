<?php

/**
 * SELECT
     [ALL | DISTINCT | DISTINCTROW ]
       [HIGH_PRIORITY]
       [STRAIGHT_JOIN]
       [SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
       [SQL_CACHE | SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
     select_expr [, select_expr ...]
     [FROM table_references
     [WHERE where_condition]
     [GROUP BY {col_name | expr | position}
       [ASC | DESC], ... [WITH ROLLUP]]
     [HAVING where_condition]
     [ORDER BY {col_name | expr | position}
       [ASC | DESC], ...]
     [LIMIT {[offset,] row_count | row_count OFFSET offset}]
     [PROCEDURE procedure_name(argument_list)]
     [INTO OUTFILE 'file_name'
         [CHARACTER SET charset_name]
         export_options
       | INTO DUMPFILE 'file_name'
       | INTO var_name [, var_name]]
     [FOR UPDATE | LOCK IN SHARE MODE]]
 */

$query = new Pdo_Query(PDO_SELECT);

// add FROM clauses
$query->setFrom('table', 'alias');
$query->setFrom('table'); // same as ->from('table', NULL);

// SELECT * FROM (SELECT 1, 2, 3) AS t1;
$query3 = 'SELECT 1, 2, 3';
$query->setFrom($query3, 't1', PDO_SUBQUERY);

// add query options
$query->addOption('DISTINCT')
      ->addOption('HIGH_PRIORITY');

// add fields to query
$query->addField('field', 'alias')
      ->addField('1 + 1', 'alias')
      ->addField('1 + 1'); // alias will be expression

// do subqueries in fields
// xxx $query2 should be a Pdo_Query really
$query2 = 'SELECT COUNT(*) FROM table2';
$query->addField($query2, 'alias', PDO_SUBQUERY);

// returns a list of fields that are expected to be returned
$query->getFields();

// delete a field
$query->deleteField('1 + 1');

// http://dev.mysql.com/doc/refman//5.5/en/join.html
// join table, alias, field1, field2, join type
$query->addJoin('right_tbl', NULL, 'left_tbl.id', 'right_tbl.id', PDO_LEFT_JOIN)
      ->addJoin('right_tbl', 'rt', 'left_tbl.id', 'right_tbl.id', PDO_LEFT_JOIN & PDO_NATURAL_JOIN & PDO_OUTER_JOIN); // NATURAL LEFT OUTER JOIN

$condition = new Pdo_Query_Condition;
$condition->name = 'filter students';
$condition->type = PDO_IS; // PDO_OR PDO_XOR, PDO_LIKE, PDO_IS_NULL, PDO_IS_NOT_NULL, PDO_NOT_IN, PDO_IN, PDO_LESS_THAN, PDO_MORE_THAN
$condition->field = 'p.type'; // could be alias
$condition->value = 'student';
$condition->group = NULL;
$query->addWhere($condition);

// studying a particular set of subjects
$condition = new Pdo_Query_Condition;
$condition->name = 'filter maths';
$condition->type = PDO_OR;
$condition->field = 's.name';
$condition->value = 'maths';
$condition->group = 'subject';
$query->addWhere($condition);

$condition = new Pdo_Query_Condition;
$condition->name = 'filter maths';
$condition->type = PDO_OR;
$condition->field = 's.name';
$condition->value = 'english';
$condition->group = 'subject';
$query->addWhere($condition);

// the same as addWhere
$query->addHaving($condition);

$query->addGroupBy('p.name');

$query->addOrderBy('p.name', PDO_DESC);

$query->setLimit(5, 10);