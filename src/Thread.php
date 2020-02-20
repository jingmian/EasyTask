<?php
namespace EasyTask;

/**
 * Class Thread
 * @package EasyTask
 */
class Thread extends \Thread
{
    /**
     * 线程执行的任务
     * @var $item
     */
    private $item;

    /**
     * 当前线程Id
     * @var int
     */
    private $creatorId;

    /**
     * 是否注册异常
     * @var bool
     */
    private $isRegError;

    /**
     * 构造函数
     * @var $item
     */
    public function __construct($item)
    {
        $this->item = $item;
        $this->isRegError = false;
    }

    /**
     * 获取当前线程Id
     * @return int
     */
    public function getThreadId()
    {
        return parent::getThreadId();
    }

    /**
     * 获取当前线程的父Id
     * @return int
     */
    public function getThreadPid()
    {
        return parent::getCurrentThreadId();
    }

    /**
     * 获取线程是否正常运行
     * @return bool|void
     */
    public function isRunning()
    {
        return parent::isRunning();
    }

    /**
     * 执行任务代码
     */
    private function execute()
    {
        $item = $this->item;
        switch ($item['type'])
        {
            case 1:
                $func = $item['func'];
                $func();
                break;
            case 2:
                call_user_func([$item['class'], $item['func']]);
                break;
            case 3:
                $object = new $item['class']();
                call_user_func([$object, $item['func']]);
                break;
            default:
                @pclose(@popen($item['command'], 'r'));
        }
    }

    /**
     * 单线程执行的任务
     */
    public function run()
    {
        //异常注册
        if (!$this->isRegError)
        {
            //Error::register();
            $this->isRegError = true;
        }

        //记录线程ID
        $this->creatorId = Thread::getCurrentThreadId();

        //循环执行任务
        while (true)
        {
            //执行任务
            $time = $this->item['time'];
            $this->execute();

            //单次任务跳出
            if ($time == 0) break;

            //Cpu休息
            sleep($time);
        }
    }
}