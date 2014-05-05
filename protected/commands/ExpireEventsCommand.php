<?php
class ExpireEventsCommand extends CConsoleCommand
{
    private $_authManager;

    public function getHelp()
    {
        $description = "DESCRIPTION\n";
        $description .= '    '. "This command EXPIRES any OPEN events whose start\n";
        $description .= '    '. "date and time occurs in the past.\n";

        return parent::getHelp() . $description;
    }

    /**
     * The default action - expire those nasty outdated events!!!
     */
    public function actionIndex()
    {
        $now = new DateTime('now');
        $now = $now->format("m/d/Y H:i:s");
        // Once this command is run, there is no going back!
        $sql = "UPDATE event "
                . "SET status_id = (SELECT s.id FROM event_status s WHERE s.name = 'EXPIRED'), "
                . "updated_by_id = 1, "
                . "updated_on = NOW() "
                . "WHERE status_id = (SELECT s.id FROM event_status s WHERE s.name = 'OPEN') "
                . "AND TO_SECONDS(CONCAT(start_date, ' ', start_time)) < TO_SECONDS(NOW()) ";
        
        $command = Yii::app()->db->createCommand($sql);
        
        $ret = $command->execute();
        
        echo 'Auto expire events was ran at: ' . $now . "\nExpired " . $ret . " events";
    }
}