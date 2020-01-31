<?php

require_once("utils.php");
require_once("exceptions.php");

if (isset($_REQUEST["operationType"])) {
    $operationType = recoge("operationType");
    try {
        $params = "";
        switch ($operationType) {
            case 'query':
                $requiredParams = [
                    "select", "from"
                ];
                if (isset($_REQUEST["where"])) {
                    $requiredParams[] = "params";
                }
                throwIfExceptionIfDoesntExist($requiredParams);
                $select = recoge("select");
                $from = recoge("from");
                $where = recoge("where");
                $params = json_encode(recoge("params"));
                break;

            default:
                errorAction(-1);
                break;
        }

        echo $params;
    } catch (Error $th) {
        errorAction(-1);
    }
} else {
    errorAction(-1);
}

function throwIfExceptionIfDoesntExist($elems) {
    foreach ($elems as $elem) {
        if (isset($_REQUEST[$elem])) {
            throw new Throwable("$elem doesn't exist");
        }
    }
    
}

function returnError()
{
    $json = json_encode((object) array("error" => true,));
    echo $json;
}
