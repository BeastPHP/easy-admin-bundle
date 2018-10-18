<?php

namespace Beast\EasyAdminBundle\Listener;

use Beast\EasyAdminBundle\Helper\Log;
use Beast\EasyAdminBundle\Helper\Rest\ExceptionExtend;
use Beast\EasyAdminBundle\Helper\Rest\RestBundleHelper;
use FOS\RestBundle\View\View;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ExistsLoaderInterface;

class BeastExceptionListener
{
    use ContainerAwareTrait;

    protected $twig;
    protected $debug;

    /**
     * @param Environment $twig
     * @param bool $debug Show error (false) or exception (true) pages by default
     */
    public function __construct(Environment $twig, $debug)
    {
        $this->twig = $twig;
        $this->debug = $debug;
    }

    /**
     * @param Request $request
     * @param $exception
     * @param DebugLoggerInterface|null $logger
     * @param string $format
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null, $format = 'json')
    {
        if ($exception instanceof ExceptionExtend) {
            $viewHandler = $this->container->get('fos_rest.view_handler');
            $message = unserialize($exception->getMessage());
            $parameters = array(
                'status' => RestBundleHelper::RESPONSE_STATUS_FALSE,
            );

            foreach ($message as $key => $item) {
                $parameters['errors'][] = [
                    'code' => $item['code'],
                    'message' => $item['message'],
                ];
            }

            $view = View::create($parameters, 200);
            $view->setFormat($format);
            $response = $viewHandler->handle($view);
            Log::writeLog($response->getContent(), RestBundleHelper::API_MODULE_TYPE, 'response');

            return $response;
        }

        if (!($exception instanceof FlattenException)) {
            $exception = FlattenException::create($exception);
        }

        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get(
            'showException',
            $this->debug
        ); // As opposed to an additional parameter, this maintains BC

        $code = $exception->getStatusCode();
        return new Response($this->twig->render(
            (string)$this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
            array(
                'status_code' => $code,
                'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception' => $exception,
                'logger' => $logger,
                'currentContent' => $currentContent,
            )
        ));
    }

    /**
     * @param int $startObLevel
     *
     * @return string
     */
    protected function getAndCleanOutputBuffering($startObLevel)
    {
        if (ob_get_level() <= $startObLevel) {
            return '';
        }

        Response::closeOutputBuffers($startObLevel + 1, true);

        return ob_get_clean();
    }

    /**
     * @param Request $request
     * @param string $format
     * @param int $code An HTTP response status code
     * @param bool $showException
     *
     * @return string
     */
    protected function findTemplate(Request $request, $format, $code, $showException)
    {
        $name = $showException ? 'exception' : 'error';
        if ($showException && 'html' == $format) {
            $name = 'exception_full';
        }

        // For error pages, try to find a template for the specific HTTP status code and format
        if (!$showException) {
            $template = sprintf('@Twig/Exception/%s%s.%s.twig', $name, $code, $format);
            if ($this->templateExists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = sprintf('@Twig/Exception/%s.%s.twig', $name, $format);
        if ($this->templateExists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $request->setRequestFormat('html');

        return sprintf('@Twig/Exception/%s.html.twig', $showException ? 'exception_full' : $name);
    }

    // to be removed when the minimum required version of Twig is >= 3.0
    protected function templateExists($template)
    {
        $template = (string)$template;

        $loader = $this->twig->getLoader();
        if ($loader instanceof ExistsLoaderInterface || method_exists($loader, 'exists')) {
            return $loader->exists($template);
        }

        try {
            $loader->getSourceContext($template)->getCode();

            return true;
        } catch (LoaderError $e) {
        }

        return false;
    }
}
