﻿<p><h4>EasyTask is an easy-to-use PHP resident memory scheduled task package</h4></p>
<a href="//shang.qq.com/wpa/qunwpa?idkey=d436563ad70f4e19d4a98b3e86cfe5272fd4a628a0d7f4c6d552b0012c55b4d7">Click on the official ＱＱ group to join</a> | <a href="./README_zh.md">Chinese document</a>
<br><br>
<p align="">
<a href="" rel="noopener noreferrer" target="_blank" rel="noopener noreferrer">
<img src="https://www.gaojiufeng.cn/static/images/stable_version.svg" style="max-width:100%;">
<img src="https://www.gaojiufeng.cn/static/images/php_version.svg" style="max-width:100%;">
<img src="https://www.gaojiufeng.cn/static/images/license.svg" style="max-width:100%;">
</a>
</p>

## <h4 style="text-align:left">  Project Introduction </h4>
<p>&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;EasyTask is a PHP resident memory timer Composer package, positioning is consistent with Javascript's setInterval timer effect, you can use it to complete tasks that need to be repeated (such as automatic cancellation of order timeout, asynchronous push of SMS mail, queue / consumer / channel Subscribers, etc.), and even handle Crontab scheduled tasks (such as synchronizing DB data from 1 am to 3 am every day, generating monthly unified reports on the 1st of every month, restarting the nginx server at 10 pm every night, etc.); built-in task abnormal reporting function, You can customize the handling of abnormal errors (such as automatic SMS notification of abnormal errors); it also supports automatic restart of abnormal task exits to make your task run more stable, and the toolkit supports the operation of windows, linux, and mac environments.
</p>

## <h4>   Operating environment </h4>

<ul>
    <li>windows：PHP>=5.4 (Rely on com_dotnet + wpc extension）<a href="https://www.kancloud.cn/a392223903/easytask/1666906" target="_blank">Installation tutorial</a></li>  
    <li>linux|mac：PHP>=5.4 (Rely on pcntl + posix extension）<a href="https://www.kancloud.cn/a392223903/easytask/1666906" target="_blank">Installation tutorial</a></li>
</ul>  

## <h4>  Composer install </h4>

~~~
  composer require easy-task/easy-task
~~~

## <h5>【One】. Quick Start-> Create Task </h5>

~~~
// init
$task = new Task();

// set up resident memory
$task->setDaemon(false);

// set project name
$task->setPrefix('EasyTask');

// set the logging runtime directory (log or cache directory)
$task->setRunTimePath('./Application/Runtime/');

// add closure function type timed task (open 2 processes, execute once every 10 seconds)
$task->addFunc(function () {
    $url = 'https://www.gaojiufeng.cn/?id=243';
    @file_get_contents($url);
}, 'request', 10, 2);

// add class method type timing task (also supports static methods) (start 1 process, execute once every 20 seconds)
$task->addClass(Sms::class, 'send', 'sendsms', 20, 1);

// add instruction-type timing tasks (start a process and execute it every 10 seconds)
$command = 'php /www/web/orderAutoCancel.php';
$task->addCommand($command,'orderCancel',10,1);

// add a closure function task, do not need a timer, execute immediately (open 1 process)
$task->addFunc(function () {
    while(true)
    {
       //todo
    }
}, 'request', 0, 1);

// visit the website through the curl command at 9:30 every night
$task->addCommand('curl https://www.gaojiufeng.cn', 'curl', '30 21 * * *', 1);

// start task
$task->start();
~~~

## <h5>【Two】. Quick Start-> Coherent Operation </h5>

~~~
$task = new Task();

// Set non-resident memory
$task->setDaemon(false)   

// set project name
->setPrefix('ThinkTask')   

// set system time zone
->setTimeZone('Asia/Shanghai')  

// set the child process to hang up and restart automatically
->setAutoRecover(true)  

// set the PHP running path, which is usually required for the Window system. You need to set it manually when the system cannot be found.
->setPhpPath('C:/phpEnv/php/php-7.0/php.exe')

/**
 * set the logging runtime directory (log or cache directory)
 */
->setRunTimePath('./Application/Runtime/')

/**
 * Close EasyTask's exception registration
 * EasyTask will no longer listen to set_error_handler / set_exception_handler / register_shutdown_function events
 */
->setCloseErrorRegister(true)

/**
 * set to receive errors or exceptions during operation (Mode 1)
 * you can customize the handling of abnormal information, such as sending them to your emails, SMS, as an early warning
 * (Not recommended, unless your code is robust)
 */
->setErrorRegisterNotify(function ($ex) {
    //Get error information | error line | error file
    $message = $ex->getMessage();
    $file = $ex->getFile();
    $line = $ex->getLine();
})

/**
 * set the Http address to receive errors or exceptions in operation (Method 2)
 * EasyTask will notify this URL and pass the following parameters:
 * errStr:errStr
 * errFile:errFile
 * errLine:errLine
 * your Url receives a POST request and can write code to send an email or SMS to notify you
 * (Recommended wording)
 */
->setErrorRegisterNotify('https://www.gaojiufeng.cn/rev.php')

// add task to execute closure function regularly
->addFunc(function () {
    echo 'Success3' . PHP_EOL;
}, 'fucn', 20, 1)   

// add a method for task execution class
->addClass(Sms::class, 'send', 'sendsms1', 20, 1)   

// add tasks to execute commands regularly
->addCommand('php /www/wwwroot/learn/curl.php','cmd',6,1)

// start task
->start();
~~~

## <h5>【Three】. Quick Start-> Command Integration </h5>

~~~
// get command
$force = empty($_SERVER['argv']['2']) ? '' : $_SERVER['argv']['2'];
$command = empty($_SERVER['argv']['1']) ? '' : $_SERVER['argv']['1'];

// configuration tasks
$task = new Task();
$task->setRunTimePath('./Application/Runtime/');
$task->addFunc(function () {
        $url = 'https://www.gaojiufeng.cn/?id=271';
        @file_get_contents($url);
    }, 'request', 10, 2);;

// execute according to the order
if ($command == 'start')
{
    $task->start();
}
elseif ($command == 'status')
{
    $task->status();
}
elseif ($command == 'stop')
{
    $force = ($force == 'force'); //whether to force stop
    $task->stop($force);
}
else
{
    exit('Command is not exist');
}

Start task: php console.php start
Query task: php console.php status
Stop Task: php console.php stop
Force close task: php console.php stop force
~~~

## <h5>【Four】. Quick Start-> Understanding output information </h5>

~~~
┌─────┬──────────────┬─────────────────────┬───────┬────────┬──────┐
│ pid │ name         │ started             │ time │ status │ ppid │
├─────┼──────────────┼─────────────────────┼───────┼────────┼──────┤
│ 32  │ Task_request │ 2020-01-10 15:55:44 │ 10    │ active │ 31   │
│ 33  │ Task_request │ 2020-01-10 15:55:44 │ 10    │ active │ 31   │
└─────┴──────────────┴─────────────────────┴───────┴────────┴──────┘
参数:
pid:task process id
name:task alias
started:task start time
time:task execution time
status:task status
ppid:daemon id
~~~

## <h5>【Five】. Advanced understanding-> recommended reading </h5>

~~~
(1). It is recommended that you use the absolute path for development, which is the standard and the norm
(2). It is forbidden to use exit / die syntax in the task, otherwise it will cause the entire process to exit
(3). Please close anti-virus software when installing Wpc extension in Windows to avoid false alarms
(4). Windows recommends to open popen, pclose method, it will automatically try to help you solve the problem of CMD output Chinese garbled, please try to use CMD administrator mode
(5). Windows prompt com () has been disabled for security reasons, please delete disable_classes = com configuration item in php.ini
(6). The log file is in the Log directory of the runtime directory, and the input and output abnormal files are marked in the Std directory of the runtime directory
(7). Normally stop the task, the task will start to exit safely after the execution is successful, force stop the task to exit the task directly, and may quit when it is being executed
(8). The development follows the synchronous start test and normal operation without any errors before setting the asynchronous operation. If there is a problem, check the log file or the standard input and output abnormal file, or feedback on the QQ group
~~~

## <h5>【Six】. Advanced Understanding-> Framework Integration Tutorial </h5>

&ensp;&ensp;[<font size=2>-> thinkphp3.2.x</font>](https://www.gaojiufeng.cn/?id=293). 

&ensp;&ensp;[<font size=2>-> thinkPhp5.x.x</font>](https://www.gaojiufeng.cn/?id=294).

&ensp;&ensp;[<font size=2>-> thinkPhp6.x.x</font>](https://www.gaojiufeng.cn/?id=328).

&ensp;&ensp;[<font size=2>-> laravelPhp6.x.x</font>](https://www.gaojiufeng.cn/?id=295).

## <h5>【Seven】. Advanced understanding-> Recommended actions </h5>

~~~
(1). It is recommended to use PHP version 7.1 or above, which supports asynchronous signals and does not depend on ticks
(2). It is recommended to install php_event to extend the millisecond timing support based on event polling
~~~

## <h5>【Eight】. Advanced understanding-> time parameters support crontab command </h5>

~~~
(1).Special expressions:
     @yearly      runs once a year is equivalent to (0 0 1 1 *)
     @annually    runs once a year and is equivalent to (0 0 1 1 *)
     @monthly     runs once a month is equivalent to (0 0 1 * *)
     @weekly      runs once a week is equivalent to (0 0 * * 0)
     @daily       runs once a day is equivalent to (0 0 * * *)
     @hourly      runs once every hour is equivalent to (0 * * * *)
(2).Standard expression:
     '30 21 * * * '      is executed once every night at 21:30
     '0 23 * * 6'        is executed once every Saturday at 23:00
     '3,15 * * * *'      every 3rd and 15th minute of the hour
     '45 4 1,10,22 * * ' is executed at 04:45 on 1/10/22 of every month
     '3,15 8-11 * * *'   is executed once every day in the 3rd and 15th minutes from 8am to 11am
     Please test other instructions yourself
     Use example / build_cron_date.php to generate a list of execution times to check whether your commands are as expected
~~~

## <h5>【Nine】. Special thanks to </h5>
~~~
(1). ThinkPHP (command line output component is based on Tp_Table component), official address: http://www.thinkphp.cn/
(2). Cron-expression (Crontab command parsing and version compatibility is based on Cron-expression), official address: https://github.com/dragonmantank/cron-expression
~~~
## <h5>【Ten】. Bug feedback</h5>
~~~
Please feedback to QQ group 777241713, thanks to the users who continue to feedback, your feedback makes EasyTask more and more stable!
~~~