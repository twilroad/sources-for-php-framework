<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-10-21 11:03
 */
namespace Notadd\Foundation\Bootstrap;

use ErrorException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

/**
 * Class HandleExceptions.
 */
class HandleExceptions
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application|\Notadd\Foundation\Application
     */
    protected $app;

    /**
     * TODO: Method bootstrap Description
     *
     * @param \Illuminate\Contracts\Foundation\Application|\Notadd\Foundation\Application $application
     *
     * @return void
     */
    public function bootstrap(Application $application)
    {
        $this->app = $application;
        error_reporting(-1);
        set_error_handler([
            $this,
            'handleError',
        ]);
        set_exception_handler([
            $this,
            'handleException',
        ]);
        register_shutdown_function([
            $this,
            'handleShutdown',
        ]);
        if (!$application->environment('testing')) {
            ini_set('display_errors', 'Off');
        }
        if ($application->make('config')->get('app.debug')) {
            ini_set('display_errors', true);
        }
    }

    /**
     * TODO: Method handleError Description
     *
     * @param int    $level
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     *
     * @throws \ErrorException
     * @return void
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * TODO: Method handleException Description
     *
     * @param \Throwable $e
     *
     * @return void
     */
    public function handleException($e)
    {
        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }
        $this->getExceptionHandler()->report($e);
        if ($this->app->runningInConsole()) {
            $this->renderForConsole($e);
        } else {
            $this->renderHttpResponse($e);
        }
    }

    /**
     * TODO: Method renderForConsole Description
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function renderForConsole(Exception $e)
    {
        $this->getExceptionHandler()->renderForConsole(new ConsoleOutput(), $e);
    }

    /**
     * TODO: Method renderHttpResponse Description
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function renderHttpResponse(Exception $e)
    {
        $this->getExceptionHandler()->render($this->app['request'], $e)->send();
    }

    /**
     * TODO: Method handleShutdown Description
     */
    public function handleShutdown()
    {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }

    /**
     * TODO: Method fatalExceptionFromError Description
     *
     * @param array    $error
     * @param int|null $traceOffset
     *
     * @return \Symfony\Component\Debug\Exception\FatalErrorException
     */
    protected function fatalExceptionFromError(array $error, $traceOffset = null)
    {
        return new FatalErrorException($error['message'], $error['type'], 0, $error['file'], $error['line'],
            $traceOffset);
    }

    /**
     * TODO: Method isFatal Description
     *
     * @param int $type
     *
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_PARSE,
        ]);
    }

    /**
     * TODO: Method getExceptionHandler Description
     *
     * @return \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected function getExceptionHandler()
    {
        return $this->app->make('Illuminate\Contracts\Debug\ExceptionHandler');
    }
}
