<?php
    
    require_once "allowlist.php";

    DEFINE("GATE_API", "YourGateAPIUrl"); // We used blebox gatebox handled by htaccess IP Authentication to prevent unauthorized access
    DEFINE("METHOD_TOGGLE", "toggle"); // Command send for Toggle Gate State
    DEFINE("METHOD_STATE", "state"); // Command for retrieve Gate State

    DEFINE("STATE_OPEN", 100); // The state if gate is open
    DEFINE("STATE_HALF", 50); // The state if gate is half open
    DEFINE("STATE_CLOSED", 0); // The state if gate is closed

    DEFINE("M_IN", "in"); // Moving Direction IN
    DEFINE("M_OUT", "out"); // Moving Direction OUT
    DEFINE("AUTH_DEVICE_ID", "YourDeviceAuthId"); // This needs to be set as the Device ID of the Axis LPR Camera as Device Id to Authorize the Device

    $plateData = json_decode(file_get_contents('php://input'), true); // Read all Json Data Posted to the Script

    if($plateData)
        if($plateData["sensorProviderID"] != AUTH_DEVICE_ID)
        {
            glog("Authentication Failed: Invalid Device ID " . $plateData["sensorProviderID"], "ERROR");
            $plateData = null;
        }

    header("Content-Type: application/json; charset=utf8");

    print json_encode([
        "success" => $plateData != null ? processData($plateData) : false
    ]);

    function processData($plateData)
    {
        if(!$plateData)
            return false; // No Data provided

        $timePlans = get_timeplans();
        $authorizedPlates = get_allowlist();
    
        $plate = str_replace(" ", "", str_replace("-", "", $plateData["plateText"]));
        $movingDirection = $plateData["carMoveDirection"];
    
        if(key_exists($plate, $authorizedPlates))
        {
            // Authorized Plate
            glog("Authorized: $plate");    

            // Check if TimePlan exists
            if(!key_exists($authorizedPlates[$plate], $timePlans))
                return false;
                
            $timePlan = $timePlans[$authorizedPlates[$plate]];
    
            if(check_timeplan($timePlan, time()))
            {
                glog("Time Access granted for $plate");    

                // Authorized to open or close gate
                // Also check moving direction
                $gateState = get_state();

                if($moveIn = ($movingDirection == M_IN && $gateState == STATE_CLOSED))
                {
                    glog("Opening Gate for $plate");
                    add_to_history($plateData["plateText"], "open");
                }

                if($moveOut = ($movingDirection == M_OUT && $gateState == STATE_OPEN))
                {
                    glog("Closing Gate for $plate");
                    add_to_history($plateData["plateText"], "close");
                }
                
                if($moveIn || $moveOut)
                    return toggle_state(); // Toggle Gate State
            }
            else
                glog("Time Access denied for $plate");    
        }else
            glog("Unauthorized: $plate");

        return false;
    }

    function check_timeplan($timePlan, $timeCheckAgainst)
    {
        if($timePlan == "always")
            return true;

        // ToDo: Check TimePlan

        return true;
    }

    function get_state()
    {
        sleep(3);

        $stateRequest = GATE_API . METHOD_STATE;
        $stateResponse = file_get_contents($stateRequest);
        $jsonObj = json_decode($stateResponse, true);
        
        return $jsonObj["gate"]["currentPos"];
    }

    function toggle_state()
    {
        $stateRequest = GATE_API . METHOD_TOGGLE;
        $stateResponse = file_get_contents($stateRequest);
        $jsonObj = json_decode($stateResponse, true);

        if($jsonObj)
            return true;

        return false;
    }

    function glog($event, $warnLevel="INFO", $time=null)
    {
        $logPath = __DIR__ . "/logs/";

        if(!isset($time))
            $time = time();
            
        $currentLogFile = date("Y-m-d", $time) . ".log";

        $ipAddr = " {" . $_SERVER["REMOTE_ADDR"] . "}";

        $logStr = date("Y-m-d H:i:s", $time) . "$ipAddr [$warnLevel] $event\r\n";

        file_put_contents($logPath . $currentLogFile, $logStr, FILE_APPEND);
    }
