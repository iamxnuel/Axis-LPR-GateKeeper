<?php

function showPage() // Wrap show Page in Function, so that Page can't be accessed directly using /views/manage
{
    if (isset($_GET["do"]))
        if ($_GET["do"] == "remove_plate")
            remove_from_allowlist($_GET["plate"]);
        elseif($_GET["do"] == "add_plate")
            add_to_allowlist($_GET["plate"], "24/7");

    $timePlans = get_timeplans();
    $allowList = get_allowlist();
    $history   = get_history();
?>
    <!DOCTYPE html>
    <html lang="de">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Control :: axGateKeeper</title>

        <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body class="text-center">
        <main class="w-100 m-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Plate</th>
                        <th scope="col">Timeplan</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    foreach ($allowList as $plate => $v) :
                    ?>
                        <tr>
                            <td><? print $plate; ?></td>
                            <td><? print $v; ?></td>
                            <td><a class="btn btn-danger" href="?do=remove_plate&plate=<? print $plate ?>">L</a></td>
                        </tr>
                    <?
                    endforeach;
                    ?>
                </tbody>
            </table>
            <form method="get" class="m-auto">
                <input type="hidden" name="do" value="add_plate">
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <label for="plate_add" class="visually-hidden">Plate</label>
                        <input type="text" name="plate" class="form-control" id="plate_add" placeholder="Plate" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-3">Add</button>
                    </div>
                </div>
            </form>
            <hr />
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Time</th>
                        <th scope="col">Plate</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $history = array_reverse($history); // Reverse history so newest is first
                    foreach ($history as $i) :
                    ?>
                        <tr>
                            <td><? print date("d.m.Y H:i:s", $i["timestamp"]); ?></td>
                            <td><? print $i["plate"]; ?></td>
                            <td><? print $i["action"] == "open" ? "opened" : "closed"; ?></td>
                        </tr>
                    <?
                    endforeach;
                    ?>
                </tbody>
            </table>
        </main>
    </body>
    </html>
<?
}
?>