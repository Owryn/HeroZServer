<?php
namespace Request;

class gameReportError{

    public function __request(){
        // SWF sends error reports as fire-and-forget
        // Log to file for debugging if needed
        $error = $_POST['error'] ?? '';
        if($error){
            $logFile = __DIR__ . '/../cache/swf_errors.log';
            $entry = date('Y-m-d H:i:s') . ' | ' . substr($error, 0, 2000) . "\n";
            @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
        }
    }

}