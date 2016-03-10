<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/12/15
 * Time: 16:22
 */

namespace BitLogic\BaseBundle\Doctrine\Walker\PostgreSQL;


use Doctrine\ORM\Query\SqlWalker;

class RowCount extends SqlWalker
{
    /**
     * Walks down a SelectClause AST node, thereby generating the appropriate SQL.
     *
     * @param $selectClause
     * @return string The SQL.
     */
    public function walkSelectClause($selectClause)
    {
        $sql = parent::walkSelectClause($selectClause);

        if ($this->getQuery()->getHint('doctrine.walker.postgresql.row_count') === true) {
            $sql = str_replace('SELECT', 'SELECT count(*) over () AS TOTAL_ROW_COUNT, ', $sql);
        }

        return $sql;
    }
}