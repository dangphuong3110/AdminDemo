<?php
function queryToSQL($query, $logQuery = false)
{
    if (is_array($query)) {
        $query = $query[0];
        $addSlashes = str_replace('?', "'?'", $query['query']);

        $sql = str_replace('%', '#', $addSlashes);

        $sql = str_replace('?', '%s', $sql);

        $sql = vsprintf($sql, $query['bindings']);

        $sql = str_replace('#', '%', $sql);

        return $sql;
    }

    if ($query instanceof \Illuminate\Database\Events\QueryExecuted) {
        $addSlashes = str_replace('?', "'?'", $query->sql);

        $sql = str_replace('%', '#', $addSlashes);

        $sql = str_replace('?', '%s', $sql);

        $sql = vsprintf($sql, $query->bindings);

        $sql = str_replace('#', '%', $sql);

        return $sql;
    }
    $addSlashes = str_replace('?', "'?'", $query->toSql());

    $sql = str_replace('%', '#', $addSlashes);

    $sql = str_replace('?', '%s', $sql);

    $sql = vsprintf($sql, $query->getBindings());

    $sql = str_replace('#', '%', $sql);

    return $sql;
}
