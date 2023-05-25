# Axis LPR GateKeeper
This is not an official Project of Axis, it is maintained by a third Person. All Name rights belong to Axis.

## Intention
We needed a solution to control our blebox gateBox Gate using LicensePlates, so we created a simple Web Application for that.

## How it Works
The Axis License Plate Recognizer App sends a HTTP Post Request to our /gatekeeper.php
This Request contains JSON Payload, which we decode and process. 
The Payload contains the Property "sensorProviderId" which is set during the "Integration" Configuration in LPR App by Property "deviceId". That Property is used to Authenticate the Device (like a Password or Token).
You can set your auth Token in "gatekeeper.php" under "AUTH_DEVICE_ID" and the same in the "deviceId"

## How to configure Axis LPR
Go to the Camera Web Interface, then Apps.
There you can find the AXIS License Plate Verifier, click "open" (ensure its started first)
After you basic configured the LPR App, goto "Integration".
Select a Profile (1-3). 
Now setup the Protocol to "HTTP POST" and insert the URL to the gatekeeper Script, for Instance "https://my-website.com/internal/gate/gatekeeper.php
After that at "Select event types to push": New and Update (you can select lost aswell, but the first two should do it)
Then select "Do not send images" and deselect "Multipart" and "Event buffer"
Before you're done, you need to set the Authentication by setting the Device ID to the value you've setup in your gatekeeper.php.
If you've done all steps, the script is ready. So select "**START** to send event data to server" and click "save".
To test the integration ensure your gate is closed (because the "Test integration" will send "carMovingDirection": "in") and you've added the Plate "AXIS1234" to your allowlist. 
Click the Test button to send the comment and if anything works your gate is now opening.
(Note: It don't matter what Plate you enter in Test Integration, its broke (my version 2.7.1), it will always send "AXIS1234")

## ToDo
- Add TimePlans and TimePlans configuration (nowadays it will always use the 24/7 plan)