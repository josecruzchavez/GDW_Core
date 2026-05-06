<?php
namespace GDW\Core\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnySimpleFunction extends Command
{
    /** @var State */
    private $state;

    /** @var ObjectManagerInterface */
    private $om;

    public function __construct(
        State $state,
        ObjectManagerInterface $om,
        string $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->om = $om;
    }

    protected function configure()
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = (string)$input->getOption('class');
        $method = (string)$input->getOption('function');
        $area = (string)$input->getOption('area');

        if ($class === '' || $method === '') {
            $output->writeln('<error>Missing parameter. Use --class and --function</error>');
            $output->writeln('<comment>Run: php bin/magento gdw:run:function --help</comment>');
            return Command::FAILURE;
        }

        // ✅ Evitar métodos mágicos o peligrosos
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $method) || strpos($method, '__') === 0) {
            $output->writeln('<error>Invalid method name.</error>');
            return Command::FAILURE;
        }

        // ✅ Set area code sin reventar si ya está seteada
        try {
            $this->state->setAreaCode($area);
        } catch (\Exception $e) {
            // área ya seteada, ignoramos
        }

        $output->writeln('<info>Running:</info> ' . $class . '::' . $method . '()');
        $start = microtime(true);

        try {
            $instance = $this->om->get($class);

            if (!method_exists($instance, $method)) {
                $output->writeln('<error>Method does not exist.</error>');
                return Command::FAILURE;
            }

            $ref = new \ReflectionMethod($instance, $method);

            if (!$ref->isPublic()) {
                $output->writeln('<error>Method is not public.</error>');
                return Command::FAILURE;
            }

            if ($ref->getNumberOfRequiredParameters() > 0) {
                $output->writeln('<error>Method requires parameters. This command only supports no-arg methods.</error>');
                return Command::FAILURE;
            }

            $result = $ref->invoke($instance);

            $elapsed = microtime(true) - $start;
            $output->writeln('<info>Done.</info> Time: ' . number_format($elapsed, 3) . 's');

            // ✅ Print result safely
            if (is_scalar($result) || $result === null) {
                $output->writeln('Result: ' . var_export($result, true));
            } else {
                $output->writeln('Result: ' . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}