<?php
class TestCommand extends CConsoleCommand
{
    private $_authManager;

    public function getHelp()
    {
        $description = "DESCRIPTION\n";
        $description .= '    '. "This command generates a hash for the admin account.\n";
        $description .= '    '. "It also finds an optimal cost to use.\n";
        $description .= '    '. "for the Rinkfinder Web Application.\n";

        return parent::getHelp() . $description;
    }

    /**
     * The default action - create the RBAC structure.
     */
    public function actionIndex()
    {
        // provide the oportunity for the use to abort the request
        $message = "This might take a second or two to complete.\n\n";
        $message .= "Would you like to continue?";

        // check the input from the user and continue if 
        // they indicated yes to the above question
        if($this->confirm($message)) {
            // Get stats on the available algos!
            $data = "hello"; 

            foreach (hash_algos() as $v) { 
                $r = hash($v, $data, false); 
                printf("%-12s %3d %s\n", $v, strlen($r), $r); 
            } 

            // First find an optimal cost to use
            $timeTarget = 0.2; 
            
            $cost = 5;
            
            do {
                $cost++;
                $start = microtime(true);
                CPasswordHelper::hashPassword("test", $cost);
                $end = microtime(true);
            } while (($end - $start) < $timeTarget);
            
            // Now generate our password using the new cost
            $hash = CPasswordHelper::hashPassword("Blah", 11); // Web host is optimal at 11
            $key_hash = hash('sha256', microtime() . "Blah");
            
            // Finally, we test to ensure we can verify the new hash
            $verified = CPasswordHelper::verifyPassword("Blah", $hash);
            
            // provide a message indicating success
            echo "Cost and hash successfully generated.\n\n";
            echo "Optimal Cost: " . $cost . "\n";
            echo "Admin Hash: " . $hash . "\n";
            echo "Hash Verified: " . ($verified ? "Yes" : "No") . "\n";
            echo "User Key Hash: " . $key_hash . "\n";
        } else {
            echo "Operation cancelled.\n";
        }
    }
}