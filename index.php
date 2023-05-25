<?php

    require_once "allowlist.php";

    session_start();

    // Just a very simple Authentication
    // Important: No BruteForce Security yet

    DEFINE("ID", 123); // The Login ID to get a two inputs based Login, increases Security against BruteForce
    DEFINE("PASSKEYS", [ // All Passwords you wish to authorize for Login
        "YourPassword1", 
        "YourPassword2"
    ]);
    
    if(isset($_POST["id"]) && isset($_POST["passkey"]))
    {
        // Authenticate
        $id = $_POST["id"];
        $key = $_POST["passkey"];        

        if($id == ID)
            if(in_array($key, PASSKEYS))
                $_SESSION["key"] = $key;
    }

    if(!isset($_SESSION["key"]) || !in_array($_SESSION["key"], PASSKEYS))
        die(authenticate());    

    // User is authenticated
    include_once "views/manage.php";
    showPage();

    function authenticate()
    {
        // User requires authentication
        include_once "views/authenticate.php";
    }