<?php
declare(strict_types=1);

namespace GDW\Core\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use JsonException;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnySimpleFunction extends Command
{
    private const EXIT_SUCCESS = 0;
    private const EXIT_FAILURE = 1;

    private const ALLOWED_CLASS_PREFIXES = [
        'GDW\\',
        'Magento\\Catalog\\Cron\\',
    ];

    private const ALLOWED_AREAS = [
        Area::AREA_FRONTEND,
        Area::AREA_ADMINHTML,
        Area::AREA_CRONTAB,
    ];

    /** @var State */
    private $state;

    /** @var ObjectManagerInterface */
    private $om;

    public function __construct(
        State $state,
        ObjectManagerInterface $om,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->om = $om;
    }

    protected function configure(): void
    {
        $this->setName('gdw:run:function')
            ->setDescription('Run a public no-arg method from a class (restricted).')
            ->addOption('class', 'c', InputOption::VALUE_REQUIRED, 'FQCN (e.g. Vendor\\Module\\Model\\X)')
            ->addOption('function', 'f', InputOption::VALUE_REQUIRED, 'Method name (no args)')
                        ->addOption('area', 'a', InputOption::VALUE_OPTIONAL, 'Area code (frontend/adminhtml)', Area::AREA_FRONTEND)
                        ->addUsage('--class="GDW\\Core\\Test\\Index" --function="anyFunction"')
                        ->addUsage('--class="Magento\\Catalog\\Cron\\SynchronizeWebsiteAttributes" --function="execute" --area="adminhtml"')
                        ->setHelp(
                                <<<'HELP'
Execute a public method without required arguments from a Magento class.

Required options:
    --class      Fully-qualified class name (FQCN)
    --function   Public method to execute (no required params)

Optional options:
    --area       Magento area code: frontend|adminhtml (default: frontend)

Examples:
    php bin/magento gdw:run:function --class="GDW\\Core\\Test\\Index" --function="anyFunction"
    php bin/magento gdw:run:function --class="Magento\\Catalog\\Cron\\SynchronizeWebsiteAttributes" --function="execute" --area="adminhtml"

Notes:
    - Only public methods are allowed.
    - Methods with required arguments are rejected.
    - Use carefully in production.
HELP
                        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = (string)$input->getOption('class');
        $method = (string)$input->getOption('function');
        $area = (string)$input->getOption('area');

        if ($class === '' || $method === '') {
            $output->writeln('<error>Missing parameter. Use --class and --function</error>');
            $output->writeln('<comment>Run: php bin/magento gdw:run:function --help</comment>');
            return self::EXIT_FAILURE;
        }

        if (!preg_match('/^(?:\\?[A-Z_a-z][A-Za-z0-9_]*)(?:\\\\[A-Z_a-z][A-Za-z0-9_]*)*$/', $class)) {
            $output->writeln('<error>Invalid class name.</error>');
            return self::EXIT_FAILURE;
        }

        if (!$this->isAllowedClass($class)) {
            $output->writeln('<error>Class is outside the allowed namespaces.</error>');
            return self::EXIT_FAILURE;
        }

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $method) || strpos($method, '__') === 0) {
            $output->writeln('<error>Invalid method name.</error>');
            return self::EXIT_FAILURE;
        }

        if (!in_array($area, self::ALLOWED_AREAS, true)) {
            $output->writeln('<error>Invalid area code. Allowed values: frontend, adminhtml, crontab.</error>');
            return self::EXIT_FAILURE;
        }

        try {
            $this->state->setAreaCode($area);
        } catch (LocalizedException $exception) {
        }

        $output->writeln('<info>Running:</info> ' . $class . '::' . $method . '()');
        $start = microtime(true);

        try {
            $instance = $this->om->get($class);

            if (!method_exists($instance, $method)) {
                $output->writeln('<error>Method does not exist.</error>');
                return self::EXIT_FAILURE;
            }

            $ref = new ReflectionMethod($instance, $method);

            if (!$ref->isPublic()) {
                $output->writeln('<error>Method is not public.</error>');
                return self::EXIT_FAILURE;
            }

            if ($ref->getNumberOfRequiredParameters() > 0) {
                $output->writeln('<error>Method requires parameters. This command only supports no-arg methods.</error>');
                return self::EXIT_FAILURE;
            }

            $result = $ref->invoke($instance);

            $elapsed = microtime(true) - $start;
            $output->writeln('<info>Done.</info> Time: ' . number_format($elapsed, 3) . 's');

            // ✅ Print result safely
            if (is_scalar($result) || $result === null) {
                $output->writeln('Result: ' . var_export($result, true));
            } else {
                $output->writeln('Result: ' . $this->encodeResult($result));
            }

            return self::EXIT_SUCCESS;
        } catch (ReflectionException $e) {
            $output->writeln('<error>Reflection error: ' . $e->getMessage() . '</error>');
            return self::EXIT_FAILURE;
        } catch (\Throwable $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return self::EXIT_FAILURE;
        }
    }

    private function isAllowedClass(string $class): bool
    {
        foreach (self::ALLOWED_CLASS_PREFIXES as $prefix) {
            if (strpos(ltrim($class, '\\'), $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private function encodeResult($result): string
    {
        try {
            return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return print_r($result, true);
        }
    }
}