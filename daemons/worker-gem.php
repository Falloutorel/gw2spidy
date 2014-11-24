<?php

use GW2Spidy\DB\GoldToGemRate;
use GW2Spidy\DB\GoldToGemRateQuery;

use GW2Spidy\DB\GemToGoldRate;
use GW2Spidy\DB\GemToGoldRateQuery;

use GW2Spidy\GemExchangeSpider;
use GW2Spidy\NewQueue\RequestSlotManager;

require dirname(__FILE__) . '/../autoload.php';

function logg($msg){
    echo "[" . date("Y-m-d H:i:s") . "] " . $msg;
}

$UUID    = getmypid() . "::" . time();
$con     = Propel::getConnection();
$run     = 0;
$max     = 100;
$debug   = in_array('--debug', $argv);

$slotManager  = RequestSlotManager::getInstance();

/*
 * $run up to $max in 1 process, then exit so process gets revived
 *  this is to avoid any memory problems (propel keeps a lot of stuff in memory)
 */
while ($run < $max) {
    $begin = microtime(true);

    $slot = $slotManager->getAvailableSlot();

    if (!$slot) {
        print "no slots, sleeping [2] ... \n";
        sleep(2);

        continue;
    }

    echo "got slot, begin [".(microtime(true) - $begin)."] \n";

    try {
        ob_start();

        $rates  = GemExchangeSpider::getInstance()->getGemExchangeRate();

        if (!$rates) {
            throw new Exception("No gem exchange data");
        }

        $date   = new DateTime();
        $date   = new DateTime($date->format('Y-m-d H:i:00'));

        if (!($exists = GoldToGemRateQuery::create()
                ->filterByRateDatetime($date)
                ->count() > 0)) {

            $goldtogem = new GoldToGemRate();
            $goldtogem->setRateDatetime($date);
            $goldtogem->setRate($rates['gold_to_gem'] * 100); // convert to copper
            $goldtogem->save();
        }

        if (!($exists = GemToGoldRateQuery::create()
                        ->filterByRateDatetime($date)
                        ->count() > 0)) {

            $goldtogem = new GemToGoldRate();
            $goldtogem->setRateDatetime($date);
            $goldtogem->setRate($rates['gem_to_gold'] * 100); // convert to copper
            $goldtogem->save();
        }

        if ($debug) {
            echo ob_get_clean();
        } else {
            ob_get_clean();
        }
    } catch (Exception $e) {
        $log = ob_get_clean();
        echo " --------------- \n !! worker process threw exception !!\n --------------- \n {$log} \n --------------- \n {$e} \n --------------- \n";

        echo "error, sleeping [60] ... \n";
        sleep(60);
        break;
    }

    echo "done [".(microtime(true) - $begin)."] \n";

    echo "sleeping [180] ... \n";
    sleep(180);

    $run++;
}

