<?php

function message($message = "Operation successful", $statusCode = 200)
{
    return response()->json(['message' => $message], $statusCode);
}
