<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\CheckPeers::class,
        Commands\checkUserLogin::class,
        Commands\sendToSlack::class,
        Commands\checkTrial::class,
        Commands\FileEncrypt::class,

    ];
    protected $cpuload;
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $this->cpuload = $this->getServerLoad();
        if (is_null($this->cpuload)) {
            $this->cpuload = "CPU load not estimateable (maybe too old Windows or missing rights at Linux or Windows)";
        }
        //  $schedule->call(function(){
        //     /* get disk space free (in bytes) */
        // $df = disk_free_space("/var/www");
        // /* and get disk space total (in bytes)  */
        // $dt = disk_total_space("/var/www");
        // // now we calculate the disk space used (in bytes)
        // $du = $dt - $df;
        // // percentage of disk used - this will be used to also set the width % of the progress bar
        //         $diskusage = sprintf('%.2f',($du / $dt) * 100);

        // if($diskusage > 30  && $this->cpuload > 30){
        //     \Slack::to('#mee2box')->send("Disk Usage: ".$diskusage." % CPU Usage: ".number_format($this->cpuload,2,'.','')."% Memory Usage: ".$this->current_memory_usage()."% ");
        // }
        // })->everyMinute();

        //     $schedule->call(function(){
        //     \Slack::to('#testing')->send(" % CPU Usage: ".$this->cpuload."%.");
        // })->everyMinute();
        
        $schedule->command('check-peers')->everyMinute();

        // clear server cache every hourly
        $schedule->command('config:cache')->hourly();
        $schedule->command('config:clear')->hourly();
        $schedule->command('cache:clear')->hourly();
        $schedule->command('optimize')->hourly();
        $schedule->command('view:clear')->hourly();

        // Remove backup for dev only
        //

        $schedule->command('check:trial')->timezone('Asia/Manila')->dailyAt('18:00');
        $schedule->command('check:login')->timezone('Asia/Manila')->dailyAt('19:00');
        //$schedule->command('backup:clean')->timezone('Asia/Manila')->dailyAt('19:00');
        //$schedule->command('backup:run')->timezone('Asia/Manila')->dailyAt('19:00');
        //$schedule->command('backup:monitor')->timezone('Asia/Manila')->dailyAt('19:00');
        //$schedule->command('backup:list')->timezone('Asia/Manila')->dailyAt('19:00');
    }

    function current_memory_usage() {

           $mem = memory_get_usage(true);

           $curr_mem = $this->convert( $mem );

           return round( ($curr_mem / $this->total_memory_usage() )*100, 2);
    }

    function convert( $mem ) {
           if ($mem < 1024) {

                $memory = $mem ;

           } elseif ($mem < 1048576) {

                $memory = round($mem / 1024, 2);

           } else {

                $memory = round($mem / 1048576, 2);

           }

           return $memory;
    }

    function total_memory_usage()
    {

          $data = explode("\n", file_get_contents("/proc/meminfo"));

          $meminfo = array();

          foreach ($data as $line) {

             list($key, $val) = explode(":", $line);

             $meminfo[$key] = trim($val);

             $mem = preg_replace("/[^0-9]{1,4}/", '', $meminfo[$key]);
             $total = round($mem / 1024 , 2);

             return $total;
          }
    }
    
    function _getServerLoadLinuxData()
    {
        if (is_readable("/proc/stat"))
        {
            $stats = @file_get_contents("/proc/stat");
            if ($stats !== false)
            {
                // Remove double spaces to make it easier to extract values with explode()
                $stats = preg_replace("/[[:blank:]]+/", " ", $stats);
                // Separate lines
                $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
                $stats = explode("\n", $stats);
                // Separate values and find line for main CPU load
                foreach ($stats as $statLine)
                {
                    $statLineData = explode(" ", trim($statLine));
                    // Found!
                    if
                    (
                        (count($statLineData) >= 5) &&
                        ($statLineData[0] == "cpu")
                    )
                    {
                        return array(
                            $statLineData[1],
                            $statLineData[2],
                            $statLineData[3],
                            $statLineData[4],
                        );
                    }
                }
            }
        }
        return null;
    }
    // Returns server load in percent (just number, without percent sign)
    function getServerLoad()
    {
        $load = null;

        if (stristr(PHP_OS, "win"))
        {
            $cmd = "wmic cpu get loadpercentage /all";
            @exec($cmd, $output);
            if ($output)
            {
                foreach ($output as $line)
                {
                    if ($line && preg_match("/^[0-9]+\$/", $line))
                    {
                        $load = $line;
                        break;
                    }
                }
            }
        }
        else
        {
            if (is_readable("/proc/stat"))
            {
                // Collect 2 samples - each with 1 second period
                // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
                $statData1 = $this->_getServerLoadLinuxData();
                sleep(1);
                $statData2 = $this->_getServerLoadLinuxData();
                if
                (
                    (!is_null($statData1)) &&
                    (!is_null($statData2))
                )
                {
                    // Get difference
                    $statData2[0] -= $statData1[0];
                    $statData2[1] -= $statData1[1];
                    $statData2[2] -= $statData1[2];
                    $statData2[3] -= $statData1[3];
                    // Sum up the 4 values for User, Nice, System and Idle and calculate
                    // the percentage of idle time (which is part of the 4 values!)
                    $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];
                    // Invert percentage to get CPU time, not idle time
                    $load = 100 - ($statData2[3] * 100 / $cpuTime);
                }
            }
        }
        return $load;
    }
    function formatSize( $bytes )
    {
            $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
            for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
                    return( round( $bytes, 2 ) . " " . $types[$i] );
    }
}



