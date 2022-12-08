<?php

function db_get($table, $where = null, $order = null, $limit = null) {
    $sql = "SELECT * FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    if ($order) {
        $sql .= " ORDER BY $order";
    }
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    $result = mysqli_query($GLOBALS['koneksi'], $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function db_get_one($table, $where = null, $order = null) {
    $data = db_get($table, $where, $order, 1);
    return $data ? $data[0] : null;
}

function db_insert($table, $data) {
    $sql = "INSERT INTO $table SET ";
    $sql .= implode(', ', array_map(function($key) use ($data) {
        return "$key = '$data[$key]'";
    }, array_keys($data)));
    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_update($table, $data, $where) {
    $sql = "UPDATE $table SET ";
    $sql .= implode(', ', array_map(function($key) use ($data) {
        return "$key = '$data[$key]'";
    }, array_keys($data)));
    $sql .= " WHERE $where";
    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_delete($table, $where) {
    $sql = "DELETE FROM $table WHERE $where";
    return mysqli_query($GLOBALS['koneksi'], $sql);
}