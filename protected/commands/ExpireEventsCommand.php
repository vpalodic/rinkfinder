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
        // Once this command is run, there is no going back!
        $sql = "UPDATE event "
                . "SET status_id = (SELECT s.id FROM event_status s WHERE s.name = 'EXPIRED'), "
                . "tags = '', "
                . "updated_by_id = 1, "
                . "updated_on = NOW() "
                . "WHERE status_id = (SELECT s.id FROM event_status s WHERE s.name = 'OPEN') "
                . "AND TO_SECONDS(CONCAT(start_date, ' ', start_time)) < TO_SECONDS(NOW()) ";
        
        $transaction = null;
        
        try {
            $transaction = Yii::app()->db->beginTransaction();
            
            $eventsSql = "SELECT id, arena_id, tags FROM event "
                . "WHERE status_id IN (SELECT s.id FROM event_status s WHERE s.name != 'EXPIRED') "
                . "AND TO_SECONDS(CONCAT(start_date, ' ', start_time)) < TO_SECONDS(NOW()) ";
            
            $models = Event::model()->findAllBySql($eventsSql);
            
            foreach($models as $model) {
                if(isset($model->tags) && !is_null($model->tags) && $model->tags != '') {
                    Tag::model()->updateFrequency($model->tags, '');
                }
            }
            
            $command = Yii::app()->db->createCommand($sql);
        
            $ret = $command->execute();
            
            $now = new DateTime('now');
            $nowstr = $now->format("m/d/Y H:i:s");
            
            // job succeeded so add a record of it!
            $command->insert('cron_job_log', array(
                'name' => 'ExpireEvents',
                'succeeded' => 1,
                'output' => 'Expire events was ran at: ' . $nowstr . "\n\r\n\rExpired " . $ret . " events\n\r\n\r",
                'created_by_id' => 1,
                'created_on' => $now->format("Y-m-d H:i:s"),
                'updated_by_id' => 1,
                'updated_on' => $now->format("Y-m-d H:i:s"),
            ));
            
            $transaction->commit();
            
            echo 'Auto expire events was ran at: ' . $nowstr . "\n\r\n\rExpired " . $ret . " events\n\r\n\r";
        } catch (Exception $ex) {
            if(isset($transaction) && $transaction != null && $transaction->active) {
                $transaction->rollback();
            }
            
            if($e instanceof CDbException) {
                throw $e;
            }
                
            $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
            $message = $e->getMessage();
            
            // Well, it wasn't a database issue that caused the job to fail,
            // so let's log it in the database!
            $now = new DateTime('now');
            $nowstr = $now->format("m/d/Y H:i:s");
            
            // job failed so add a record of it!
            $command->insert('cron_job_log', array(
                'name' => 'ExpireEvents',
                'succeeded' => 0,
                'output' => 'Failed to execute at '. $nowstr . "\n\r\n\rThe SQL Update was not run: " . $message . "\n\r\n\r" . "Error Code: " . (int)$e->getCode(),
                'created_by_id' => 1,
                'created_on' => $now->format("Y-m-d H:i:s"),
                'updated_by_id' => 1,
                'updated_on' => $now->format("Y-m-d H:i:s"),
            ));                
            
            throw new CDbException(
                    'Failed to execute the SQL statement: ' . $message,
                    (int)$e->getCode(),
                    $errorInfo
            );
        }
    }
}