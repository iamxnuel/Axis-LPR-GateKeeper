<?php

    function __allowList()
    {
        return __DIR__ . "/lists/allow.json";
    }

    function __timePlans()
    {
        return __DIR__ . "/lists/times.json";
    }

    function __history()
    {
        return __DIR__ . "/lists/history.json";
    }

    function get_history()
    {
        return json_decode(file_get_contents(__history()), true) ?? [];
    }

    function get_timeplans()
    {
        return json_decode(file_get_contents(__timePlans()), true) ?? [];
    }

    function get_allowlist()
    {
        return json_decode(file_get_contents(__allowList()), true) ?? [];
    }

    function add_to_allowlist($plate, $timePlan)
    {
        $plate = str_replace(" ", "", $plate);
        $plate = str_replace("-", "", $plate);
        $plate = str_replace(":", "", $plate);
        $plate = strtoupper(trim($plate));

        if(empty($plate))
            return;

        $allowList = get_allowlist();

        $allowList[$plate] = $timePlan;

        file_put_contents(__allowList(), json_encode($allowList));
    }

    function remove_from_allowlist($plate)
    {
        $allowList = get_allowlist();

        if(key_exists($plate, $allowList))
            unset($allowList[$plate]);

        file_put_contents(__allowList(), json_encode($allowList));
    }
    
    function add_to_history($plate, $actor)
    {
        $history = get_history();

        $history[] = [
            "timestamp" => time(),
            "plate" => $plate,
            "action" => $actor
        ];

        file_put_contents(__history(), json_encode($history));
    }